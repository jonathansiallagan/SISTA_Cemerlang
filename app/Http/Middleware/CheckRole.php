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
        if (Auth::check() && in_array(Auth::user()->role, $roles, true)) {
            return $next($request);
        }

        // User sudah login tapi salah halaman -> arahkan ke dashboard miliknya sendiri
        if (Auth::check() && $request->routeIs('admin', 'cs')) {
            return redirect()->route(Auth::user()->homeRoute());
        }

        abort(403, 'AKSES DITOLAK: Halaman ini hanya untuk ' . implode(' / ', $roles) . '.');
    }
}