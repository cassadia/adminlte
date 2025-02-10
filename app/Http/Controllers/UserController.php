<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        $emailUser = auth()->user()->email;
        $perPage = $request->input('perPage', 5);
        $keyword = $request->input('keyword');

        $users = User::whereNull('deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })
            ->paginate($perPage);

        $menusdua = $this->userRoleService->getUserRole($emailUser);

        $content = ContentService::getContent();

        return view('users.index', compact('users', 'menusdua', 'content'));
    }

    public function create(): View
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $menus = $this->getMenu();
        $content = ContentService::getContent();

        return view('users.create', compact('menusdua','menus','content'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'nmUser' => 'required|min:5',
            'emailUser' => 'required|min:10|email', // Ubah 'email' menjadi 'emailUser' dan tambahkan unique:users,email
            'password' => 'required|confirmed|min:6', // Gunakan 'confirmed' untuk memastikan bahwa 'Password' dan 'KonfPassword' sama
        ], [
            'nmUser.required' => 'Nama tidak boleh kosong.',
            'nmUser.min' => 'Nama minimal harus terdiri dari 5 karakter.',
            'emailUser.required' => 'Email tidak boleh kosong.',
            'emailUser.min' => 'Email minimal harus terdiri dari 10 karakter.',
            'emailUser.email' => 'Email harus dalam format yang benar.',
            'emailUser.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ]);

        $existingUser = User::withoutGlobalScopes()
            ->where('email', $request->emailUser)->first();

        if ($existingUser) {
            return redirect()->back()->withInput()->withErrors(['emailUser' => 'Email sudah ada di database!'])->with(['error' => 'Email sudah ada di database!']);
        }

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        $menus = $request->menu;

        $detailRoutes = DB::table('user_menu as a')
            ->join('user_menu_detail as b', 'b.master_route', '=', 'a.id')
            ->select('b.detail_route', 'b.id')
            ->where('a.status', 'Aktif')
            ->whereNull('a.deleted_at')
            ->whereIn('a.route', $menus)
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

        Permission::create([
            'name' => 'profile.show',
            'user_id' => $getId->id
        ]);

        Permission::create([
            'name' => 'profile.update',
            'user_id' => $getId->id
        ]);

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);

        //get post by ID
        $users = User::findOrFail($id);

        $getMenu = DB::table('user_menu as um')
            ->select('um.menu')
            ->selectRaw('CASE WHEN (SELECT 1 FROM users u JOIN user_assign ua ON ua.kd_user = u.id AND ua.id_user_permission = um.id WHERE u.email = ? limit 1) IS NULL THEN 0 ELSE 1 END AS menuakses', [$users->email])
            ->get();

        $content = ContentService::getContent();

        //render view with post
        return view('users.show', compact('users', 'getMenu', 'menusdua', 'content'));
    }

    public function edit(string $id): View
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);

        //get post by ID
        $users = User::findOrFail($id);

        $getMenu = DB::table('user_menu as um')
            ->select('um.menu')
            ->selectRaw('CASE WHEN (SELECT 1 FROM users u JOIN user_assign ua ON ua.kd_user = u.id AND ua.id_user_permission = um.id WHERE u.email = ? limit 1) IS NULL THEN 0 ELSE 1 END AS menuakses', [$users->email])
            ->get();

        $content = ContentService::getContent();

        //render view with post
        return view('users.edit', compact('users', 'menusdua', 'getMenu', 'content'));
    }

    public function update(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'nmUser' => 'required|min:5',
            'emailUser' => 'required|min:10|email', // Ubah 'email' menjadi 'emailUser' dan tambahkan unique:users,email
        ], [
            'nmUser.required' => 'Nama tidak boleh kosong.',
            'nmUser.min' => 'Nama minimal harus terdiri dari 5 karakter.',
            'emailUser.required' => 'Email tidak boleh kosong.',
            'emailUser.min' => 'Email minimal harus terdiri dari 10 karakter.',
            'emailUser.email' => 'Email harus dalam format yang benar.',
            'emailUser.unique' => 'Email sudah digunakan.',
        ]);


        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        $menusBefore = array_filter($request->before, function($value) {
            return $value !== null;
        });
        $menusAfter = $request->after;
        $getId = User::withoutGlobalScopes()
            ->where('email', $request->emailUser)
            ->first();

        $jsonString = json_encode($getId);
        $dataJson = json_decode($jsonString, true);

        foreach ($menusBefore as $menu) {
            $getMenus = DB::table('user_menu as a')
                ->join('user_menu_detail as b', 'b.master_route', '=', 'a.id')
                ->select('b.detail_route', 'a.id as idAssign')
                ->where('a.status', 'Aktif')
                ->whereNull('a.deleted_at')
                ->where('a.menu', $menu)
                ->get();

            $menus = $getMenus->pluck('idAssign')->toArray();
            $assigns = array_unique($menus);

            foreach ($getMenus as $menus) {
                DB::table('permissions')
                    ->where('name', $menus->detail_route)
                    ->where('user_id', $getId->id)
                    ->delete();
            }

            foreach ($assigns as $assign) {
                DB::table('user_assign')
                    ->where('kd_user', $getId->id)
                    ->where('id_user_permission', $assign)
                    ->delete();
            }
        }

        foreach ($menusAfter as $menu) {
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
            }

            $menus = $detailRoutes->pluck('idAssign')->toArray();
            $assign = array_unique($menus);

            DB::table('user_assign')->insert([
                'kd_user' => $getId->id,
                'id_user_permission' => $assign[0],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        //update product without image
        User::where("email", $request->emailUser)->update([
            'name' => $request->nmUser,
            'email' => $request->emailUser,
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $users = User::findOrFail($id);

        //delete post
        $users->delete();

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function search(Request $request)
    {

        if ($request->ajax()) {

            $data=User::where('id', 'like', '%'.$request->search.'%')
            ->orwhere('name', 'like', '%'.$request->search.'%')
            ->orwhere('email', 'like', '%'.$request->search.'%')->get();

            $output='';
            if (count($data)>0) {
                $output ='
                    <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach ($data as $row) {
                            $output .='
                            <tr>
                            <th scope="row">'.$row->id.'</th>
                            <td>'.$row->name.'</td>
                            <td>'.$row->email.'</td>
                            </tr>
                            ';
                        }
                $output .= '
                    </tbody>
                    </table>';
            } else {
                $output .='No results';
            }
            return $output;
        }
    }

    private function getMenu()
    {
        return DB::table('user_menu as a')
            ->select('a.menu', 'a.route')
            ->where('a.status', 'Aktif')
            ->whereNull('deleted_at')
            ->get();
    }

    // public function showUser($id)
    // {
    //     $response = Http::get(env('APP_URL') . 'api/getUser', [
    //         'id' => $id
    //     ]);

    //     dd($response)->all();

    //     if ($response.ok()) {
    //         $user = $response->json()['data'];

    //         dd($user)->all();
    //     }
    // }

    public function getUserId(Request $request)
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        return view('show.user', compact('menusdua','content'));
        // $users = User::findOrFail($id);

        // if ($users) {
        //     return response()->json($data, 200, $headers);
        // }

        // return response()->json($data, 400, $headers);
    }
}
