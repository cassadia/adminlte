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

        // Jika pengguna memiliki akses ke /public/, arahkan jika belum ada di dalamnya
        if ($user && $user->has_public_path) {
            if (!str_starts_with($request->path(), 'public')) {
                return redirect('/public/' . $request->path());
            }
        }

        return $next($request);
    }
}
