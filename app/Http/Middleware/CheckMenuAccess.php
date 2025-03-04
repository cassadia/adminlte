<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

// class CheckMenuAccess
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         $user = auth()->user();
//         $routeName = $request->route()->getName();

//         // Contoh logika:
//         // if (! $user->hasPermission($routeName)) {
//         //     abort(403, 'Unauthorized');
//         // }

//         // if (!$this->hasAccessToMenu($user, $routeName)) {
//         //     // Redirect ke halaman fallback
//         //     // return Redirect::route('fallback.dashboard');
//         //     return Redirect::route('public.profile.show');
//         // }

//         var_dump('checkMenuAccess 1');
//         // var_dump($routeName);
//         // var_dump($user->permissions->pluck('name')->toArray());

//         if ($routeName === 'public.profile.show') {
//             var_dump('checkMenuAccess 2');
//             return $next($request);
//         }

//         // Cek akses pengguna ke menu
//         if (!$this->hasAccessToMenu($user, $routeName)) {
//             var_dump('checkMenuAccess 3');
//             // Redirect ke fallback route
//             return Redirect::route('public.profile.show');
//         }

//         return $next($request);
//     }

//     private function hasAccessToMenu($user, $menu)
//     {
//         // var_dump($user->permissions);
//         // Logika untuk memeriksa akses pengguna ke menu
//         // return in_array($menu, $user->permissions ?? []);
//         $permissions = $user->permissions->pluck('name')->toArray();
//         return in_array($menu, $permissions);
//     }
// }


class CheckMenuAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $routeName = $request->route()->getName();

        // Pengecualian untuk fallback route
        if ($routeName === 'profile.show') {
            return $next($request);
        }

        \Log::info('Middleware executed for route: ' . $request->route()->getName());

        // Cek akses pengguna ke menu
        // if (!$this->hasAccessToMenu($user, $routeName)) {
        //     // Redirect ke fallback route
        //     return Redirect::route('public.profile.show');
        // }
        if (!$this->hasAccessToMenu($user, $routeName)) {
            // Redirect ke fallback route berdasarkan status has_public_path
            if ($user->has_public_path) {
                return Redirect::route('public.profile.show');
            } else {
                return Redirect::route('home'); // Fallback non-publik
            }
        }

        return $next($request);
    }

    private function hasAccessToMenu($user, $menu)
    {
        $permissions = $user->permissions->pluck('name')->toArray();
        return in_array($menu, $permissions);
    }
}
