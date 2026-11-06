<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Pemakaian: role:Admin Pusat  atau  role:Admin Pusat,Admin Cabang
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        // Lolos pengecekan jika user sudah login dan role-nya cocok
        if ($user && in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // Jika user login tapi mencoba mengakses halaman role lain
        if ($user) {
            $redirectRoute = match ($user->role) {
                'Admin Pusat', 'Admin Cabang' => 'admin.dashboard',
                'Customer Service' => 'cs.dashboard',
                default => 'login',
            };

            return redirect()->route($redirectRoute);
        }

        // Jika belum login atau kondisi tidak terduga
        abort(403, 'AKSES DITOLAK: Halaman ini hanya untuk ' . implode(' / ', $roles) . '.');
    }
}

// Memaksa update cache docker: versi 1.0.1