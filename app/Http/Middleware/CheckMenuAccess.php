<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $routeName = $request->route()->getName();

        // Contoh logika:
        // if (! $user->hasPermission($routeName)) {
        //     abort(403, 'Unauthorized');
        // }

        // if (!$this->hasAccessToMenu($user, $routeName)) {
        //     // Redirect ke halaman fallback
        //     // return Redirect::route('fallback.dashboard');
        //     return Redirect::route('public.profile.show');
        // }

        if ($routeName === 'public.profile.show') {
            return $next($request);
        }

        // Cek akses pengguna ke menu
        if (!$this->hasAccessToMenu($user, $routeName)) {
            // Redirect ke fallback route
            return Redirect::route('public.profile.show');
        }

        return $next($request);
    }

    private function hasAccessToMenu($user, $menu)
    {
        // var_dump($user->permissions);
        // Logika untuk memeriksa akses pengguna ke menu
        // return in_array($menu, $user->permissions ?? []);
        $permissions = $user->permissions->pluck('name')->toArray();
        return in_array($menu, $permissions);
    }
}
