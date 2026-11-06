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

        if ($user && in_array($user->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'AKSES DITOLAK: Halaman ini hanya untuk ' . implode(' / ', $roles) . '.');
    }
}
