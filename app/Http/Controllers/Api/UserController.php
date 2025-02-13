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
        $id = $request->input('id');

        $users = User::whereNull('deleted_at')
            ->where('id', $id)
            ->select(
                'users.*',
                DB::raw('DATE_FORMAT(users.created_at, "%Y-%m-%d %H:%i:%s") as format_createdAt'),
                DB::raw('DATE_FORMAT(users.updated_at, "%Y-%m-%d %H:%i:%s") as format_updatedAt')
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
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 404);
    }

    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'nmUser' => 'required|string|max:255',
            'emailUser' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'menu' => 'array',
            'status' => 'boolean'
        ]);
    }

    public function updateUser(Request $request)
    {
        $getId = User::where('email', $request->emailUser)
            ->first();

        $cekPermission = DB::select('select * from permissions where user_id = ?', [$getId->id]);
        if (count($cekPermission) > 0) {
            DB::delete('delete from permissions where user_id = ?', [$getId->id]);
            DB::delete('delete from user_assign where kd_user = ?', [$getId->id]);
        }

        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
        $dataPublic = $request->has('public') ? 1 : 0;

        foreach ($request->menu as $menu) {
            $detailRoutes = DB::table('user_menu as a')
                ->join('user_menu_detail as b', 'b.master_route', '=', 'a.id')
                ->select('b.detail_route', 'b.id', 'a.id as idAssign')
                ->where('a.status', 'Aktif')
                ->whereNull('a.deleted_at')
                ->where('a.menu', $menu)
                ->get();

            foreach ($detailRoutes as $route) {

                Permission::create([
                    'name' => $route->detail_route,
                    'user_id' => $getId->id
                ]);

                // $publicHome = $dataPublic && $route->detail_route == 'home' ? 'public.home' : $route->detail_route;

                // if ($dataPublic && $route->detail_route == 'home') {
                //     Permission::create([
                //         'name' => 'home',
                //         'user_id' => $getId->id
                //     ]);

                //     Permission::create([
                //         'name' => 'public.home',
                //         'user_id' => $getId->id
                //     ]);
                // } else {
                //     Permission::create([
                //         'name' => $publicHome,
                //         'user_id' => $getId->id
                //     ]);
                // }
            }

            Permission::create([
                'name' => 'profile.show',
                'user_id' => $getId->id
            ]);

            Permission::create([
                'name' => 'profile.update',
                'user_id' => $getId->id
            ]);

            $menus = $detailRoutes->pluck('idAssign')->toArray();
            $assign = array_unique($menus);

            DB::table('user_assign')->insert([
                'kd_user' => $getId->id,
                'id_user_permission' => $assign[0],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        User::where("email", $request->emailUser)->update([
            'name' => $request->nmUser,
            'email' => $request->emailUser,
            'status' => $status,
            'has_public_path' => $dataPublic,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ], 200);
    }

    public function updatePass(Request $request)
    {
        $getId = User::where('email', $request->emailUser)
            ->first();

        if ($getId) {
            if (Hash::check($request->oldPass, $getId->password)) {
                $getId->update([
                    'name' => $request->nmUser,
                    'email' => $request->emailUser,
                    'password' => Hash::make($request->newPass),
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Old password is incorrect'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }
}
