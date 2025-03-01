<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

use App\Models\User;
use App\Models\Permission;

use App\Services\UserRoleService;
use App\Services\ContentService;

class UserController extends Controller
{
    protected $userRoleService;

    public  function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage');
        $keyword = $request->input('keyword');

        // dd($request)->all();

        $users = User::whereNull('deleted_at')
        ->when($keyword, function ($query) use ($keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%');
        })
        ->select(
            'users.*',
            DB::raw('DATE_FORMAT(users.created_at, "%Y-%m-%d %H:%i:%s") as format_createdAt'),
            DB::raw('DATE_FORMAT(users.updated_at, "%Y-%m-%d %H:%i:%s") as format_updatedAt')
        )
        ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    public function getUserById(Request $request)
    {
        try {
            $id = $request->input('id');

            $users = User::whereNull('deleted_at')
                ->where('id', $id)
                ->select(
                    'users.*',
                    DB::raw('DATE_FORMAT(users.created_at, "%Y-%m-%d %H:%i:%s") as format_createdAt'),
                    DB::raw('DATE_FORMAT(users.updated_at, "%Y-%m-%d %H:%i:%s") as format_updatedAt'),
                    DB::raw('DATE_FORMAT(users.expires_at, "%Y-%m-%d %H:%i:%s") as format_expiredAt')
                )
                ->first();

            $menus = DB::table('user_menu as um')
                ->select('um.menu')
                ->selectRaw('CASE WHEN (
                    SELECT 1 FROM users u JOIN user_assign ua ON ua.kd_user = u.id AND ua.id_user_permission = um.id
                    WHERE u.email = ? limit 1
                ) IS NULL THEN 0 ELSE 1 END AS menuakses', [$users->email])
                ->get();

            if ($users) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'data' => $users,
                        'menus' => $menus
                    ]
                ], 200)->header('Cache-Control', 'no-store');
            }
        } catch (\Throwable $th) {
            //throw $th;

            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404)->header('Cache-Control', 'no-store');
        }
    }

    public function createUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'nmUser' => 'required|string|max:255',
                'emailUser' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'menu' => 'array',
                'status' => 'boolean'
            ]);

            $existingUser = User::withoutGlobalScopes()
                ->where('email', $request->emailUser)->first();

            if ($existingUser) {
                return redirect()->back()->withInput()->withErrors(['emailUser' => 'Email sudah ada di database!'])->with(['error' => 'Email sudah ada di database!']);
            }

            // Tentukan nilai status berdasarkan kondisi checkbox
            $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
            $menus = $request->menu;
            $dataPublic = $request->input('dataPublic', 0);

            $detailRoutes = DB::table('user_menu as a')
                ->join('user_menu_detail as b', 'b.master_route', '=', 'a.id')
                ->select('b.detail_route', 'b.id')
                ->where('a.status', 'Aktif')
                ->whereNull('a.deleted_at')
                ->whereIn('a.route', $menus)
                ->when($dataPublic == 1, function ($query) {
                    return $query->where('b.has_public_path', 1);
                })
                ->when($dataPublic != 1, function ($query) {
                    return $query->where('b.has_public_path', 0);
                })
                ->get();

            $detailAssign = DB::table('user_menu')
                ->whereIn('route', $menus)
                ->where('status', 'Aktif')
                ->get();

            //create post
            User::create([
                'name' => $request->nmUser,
                'email' => $request->emailUser,
                'password' => Hash::make($request->password),
                'status' => $status,
                'has_public_path' => $dataPublic,
                'expires_at' => $request->expiredTime,
            ]);

            $getId = User::withoutGlobalScopes()
                ->where('email', $request->emailUser)
                ->first();

            foreach ($detailRoutes as $route) {
                Permission::create([
                    'name' => $route->detail_route,
                    'user_id' => $getId->id
                ]);
            }

            foreach ($detailAssign as $assign) {
                DB::table('user_assign')->insert([
                    'kd_user' => $getId->id,
                    'id_user_permission' => $assign->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User inserted successfully'
            ], 200)->header('Cache-Control', 'no-store');
        } catch (\Throwable $th) {
            //throw $th;

            // \Log::info('Insert user:', $th);

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'User inserted failed'
            ], 404)->header('Cache-Control', 'no-store');
        }
    }

    public function updateUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $getId = User::where('email', $request->emailUser)
            ->first();

            $cekPermission = DB::select('select * from permissions where user_id = ?', [$getId->id]);
            if (count($cekPermission) > 0) {
                DB::delete('delete from permissions where user_id = ?', [$getId->id]);
                DB::delete('delete from user_assign where kd_user = ?', [$getId->id]);
            }

            $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
            $dataPublic = $request->has('dataPublic') ? 1 : 0;

            foreach ($request->menu as $menu) {
                $detailRoutes = DB::table('user_menu as a')
                    ->join('user_menu_detail as b', 'b.master_route', '=', 'a.id')
                    ->select('b.detail_route', 'b.id', 'a.id as idAssign')
                    ->where('a.status', 'Aktif')
                    ->whereNull('a.deleted_at')
                    ->where('a.menu', $menu)
                    ->when($dataPublic == 1, function ($query) {
                        return $query->where('b.has_public_path', 1);
                    })
                    ->when($dataPublic != 1, function ($query) {
                        return $query->where('b.has_public_path', 0);
                    })
                    ->get();

                foreach ($detailRoutes as $route) {
                    Permission::create([
                        'name' => $route->detail_route,
                        'user_id' => $getId->id
                    ]);
                }

                $menus = $detailRoutes->pluck('idAssign')->toArray();
                $assign = array_unique($menus);

                if (!empty($assign)) {
                    DB::table('user_assign')->insert([
                        'kd_user' => $getId->id,
                        'id_user_permission' => $assign[0],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    \Log::warning("No valid assignments found for menu: {$menu}");
                }
            }

            User::where("email", $request->emailUser)->update([
                'name' => $request->nmUser,
                'email' => $request->emailUser,
                'status' => $status,
                'has_public_path' => $dataPublic,
                'expires_at' => $request->expiredTime,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully'
            ], 200)->header('Cache-Control', 'no-store');
        } catch (\Throwable $th) {
            //throw $th;

            \Log::info('Update user:', $th);

            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'User updated failed'
            ], 404)->header('Cache-Control', 'no-store');
        }
    }

    public function updatePassOld(Request $request)
    {
        $getId = User::where('email', $request->emailUser)
            ->first();

        if ($getId) {
            if ($request->oldPass !== null) {
                if (Hash::check($request->oldPass, $getId->password)) {
                    $getId->update([
                        'name' => $request->nmUser,
                        'email' => $request->emailUser,
                        'password' => Hash::make($request->newPass),
                    ]);
                }
            } else {
                $getId->update([
                    'name' => $request->nmUser,
                    'email' => $request->emailUser,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil diperbaharui!.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    public function updatePass(Request $request)
    {
        try {
            $user = User::where('email', $request->emailUser)->firstOrFail();

            if ($request->oldPass !== null && !Hash::check($request->oldPass, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Old password is incorrect'
                ], 400);
            }

            $updateData = [
                'name' => $request->nmUser,
                'email' => $request->emailUser,
            ];

            if ($request->newPass) {
                $updateData['password'] = Hash::make($request->newPass);
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil diperbaharui!.'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $user = User::where('email', $request->userEmail)->first();
            if (!$user) {
                return response()->json(['message' => 'Pengguna tidak diketemukan'], 404);
            }

            $permission = Permission::where('user_id', $request->userId)->first();
            if (!$permission) {
                return response()->json(['message' => 'Ijin tidak diketemukan'], 404);
            }

            $userAssign = DB::table('user_assign')
                ->where('kd_user', $request->userId)
                ->get();

            // Debugging: Tampilkan data yang ditemukan
            \Log::info([
                'user' => $user,
                'permission' => $permission,
                'userAssign' => $userAssign,
            ]);

            $user->delete();
            $permission->delete();
            DB::table('user_assign')->where('kd_user', $request->userId)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data user berhasil dihapus!'
            ], 200)->header('Cache-Control', 'no-store');
        } catch (\Throwable $th) {
            throw $th;

            return response()->json([
                'status' => 'failed',
                'message' => 'Data user gagal dihapus!'
            ], 500)->header('Cache-Control', 'no-store');
        }
    }
}
