<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/users';

    protected function redirectTo()
    {
        $emailUser = auth()->user()->email;
        $user = User::where('email', $emailUser)->first();

        // {{ route($menu->menu_link) }}

        $cekRoute = DB::table('users as a')
            ->select('um.menu_link')
            ->join('user_assign as ua', 'ua.kd_user', '=', 'a.id')
            ->join('user_menu as um', 'um.id', '=', 'ua.id_user_permission')
            ->where('a.email', $emailUser)
            ->first();

        $token = $user->createToken('api-token')->plainTextToken;

        // $expiresTime = now()->addMinutes(60);

        User::where("email", $emailUser)->update([
            'bearer_token' => $token,
            // 'expires_at' => $expiresTime,
        ]);

        // Simpan token ke session
        session([
            'api_token' => $token,
            'id' => $user->id,
            'email' => $user->email,
            'public_path' => $user->has_public_path,
        ]);

        return route($cekRoute->menu_link);
    }


    public function logout(Request $request)
    {
        // Logout pengguna
        Auth::logout();

        // Hapus sesi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login atau halaman publik lainnya
        return redirect()->route('login');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
