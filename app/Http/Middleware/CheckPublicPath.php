<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPublicPath
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Pengecualian untuk fallback route
        // if ($request->route()->getName() === 'public.profile.show') {
        //     return $next($request);
        // }

        // Jika pengguna memiliki akses ke /public/, arahkan jika belum ada di dalamnya
        if ($user && $user->has_public_path) {
            if (!str_starts_with($request->path(), 'public')) {
                return redirect('/public/' . $request->path());
            }
        } else {
            // Jika pengguna tidak memiliki akses ke /public/, pastikan mereka tidak dipaksa ke /public/
            if (str_starts_with($request->path(), 'public')) {
                return redirect('/' . substr($request->path(), 7)); // Hapus prefix '/public/'
            }
        }

        return $next($request);
    }
}
