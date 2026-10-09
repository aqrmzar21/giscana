<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();
        $allowedRoles = ['admin', 'staff', 'pimpinan'];

        $hasAllowedRole = in_array($user->role, $allowedRoles) || $user->hasAnyRole($allowedRoles);

        if (!$hasAllowedRole) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman panel ini.');
        }

        return $next($request);
    }
}
