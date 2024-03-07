<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

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

        // {{ route($menu->menu_link) }}
        
        $cekRoute = DB::table('users as a')
            ->select('um.menu_link')
            ->join('user_assign as ua', 'ua.kd_user', '=', 'a.id')
            ->join('user_menu as um', 'um.id', '=', 'ua.id_user_permission')
            ->where('a.email', $emailUser)
            ->first();

        return route($cekRoute->menu_link);
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
