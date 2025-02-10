<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function apiLogin(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // return response()->json(['message' => 'Invalid credentials'], 401);
            return redirect()->back()->withErrors(['email' => 'Email atau password salah.']);
        }

        // Buat token API
        $token = $user->createToken('api-token')->plainTextToken;

        // $emailUser = auth()->user()->email;
        $cekRoute = DB::table('users as a')
            ->select('um.menu_link')
            ->join('user_assign as ua', 'ua.kd_user', '=', 'a.id')
            ->join('user_menu as um', 'um.id', '=', 'ua.id_user_permission')
            ->where('a.email', $request->email)
            ->first();
        // return route($cekRoute->menu_link);

        $expiresTime = now()->addMinutes(120);

        User::where("email", $user)->update([
            'bearer_token' => $token,
            'expires_at' => $expiresTime,
        ]);

        // // Simpan token ke session
        session([
            'api_token' => $token,
            'id' => $user->id,
            'email' => $user->email,
            'public_path' => $user->has_public_path,
        ]);

        // Response token
        // return response()->json([
        //     'message' => 'Login successful',
        //     'token' => $token,
        //     'route' => $cekRoute->menu_link
        // ]);

        return redirect()->route($cekRoute->menu_link)->with([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    // public function apiLogout(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user) {
    //         \Log::info('User attempting to logout:', ['user' => $user->id]);

    //         // Hapus token akses saat ini
    //         $user->currentAccessToken()->delete();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Logout successful',
    //             'redirect' => route('login'),
    //         ], 200);
    //     } else {
    //         \Log::error('No authenticated user found.');

    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }

    //     // if ($request->user()) {

    //     //     // Debug user data
    //     //     \Log::info('User attempting to logout:', ['user' => $request->user()]);

    //     //     $request->tokens()->delete(); // Hapus semua token autentikasi
    //     //     $request->user()->currentAccessToken()->delete();

    //     //     return response()->json(['message' => 'Logout successful'], 200);
    //     // } else {
    //     //     \Log::error('No authenticated user found.');

    //     //     return response()->json(['message' => 'Unauthorized'], 401);
    //     // }

    //     // $request->user()->currentAccessToken()->delete();

    //     // return response()->json(['message' => 'Logged out'], 200);
    // }


    // public function apiLogout(Request $request)
    // {
    //     if ($request->user()) {
    //         // Hapus token
    //         $request->user()->tokens()->delete();

    //         // Logout
    //         Auth::logout();
    //         session()->invalidate();
    //         session()->regenerateToken();

    //         \Log::info('Session setelah logout:', session()->all()); // 🔍 Cek apakah session masih ada

    //         return response()->json(['message' => 'Logout successful', 'redirect' => route('login')], 200);
    //     }

    //     return response()->json(['message' => 'Unauthorized'], 401);
    // }

    public function apiLogout(Request $request)
    {
        if ($request->user()) {
            // Hapus semua token pengguna
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logout successful',
                'redirect' => route('login') // Kirim URL login untuk redirect di frontend
            ], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
