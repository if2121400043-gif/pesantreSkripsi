<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        $activeRole = $user->active_role;

        if (!$activeRole || !$activeRole->role) {
            abort(403, 'Anda tidak memiliki peran aktif.');
        }

        $roleName = $activeRole->role->nama;

        // If roles array is empty, just check if user has ANY active role (already did above)
        if (empty($roles)) {
            return $next($request);
        }

        // If Super Admin, allow everything
        if ($roleName === 'SUPER_ADMIN') {
            return $next($request);
        }

        // Check if the user's active role is in the allowed roles
        if (in_array($roleName, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Peran Anda tidak diizinkan mengakses halaman ini.');
    }
}
