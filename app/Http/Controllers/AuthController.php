<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // === FITUR LOGIN ===

    public function login()
    {
        return view('auth.login'); 
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Arahkan sesuai role: Admin -> route 'admin', Customer Service -> route 'cs'
            return redirect()->route(Auth::user()->homeRoute());
        }

        return back()->withErrors([
            'username' => 'Username atau password yang dimasukkan salah.',
        ])->onlyInput('username');
    }

    // === FITUR REGISTER ===
    
    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route(Auth::user()->homeRoute())->with('success', 'Akun pengguna baru berhasil ditambahkan!');
    }

    // === FITUR LOGOUT ===

    public function logout(Request $request)
    {
        // 1. Keluarkan pengguna dari sistem
        Auth::logout();

        // 2. Hapus sesi yang aktif saat ini untuk keamanan
        $request->session()->invalidate();

        // 3. Buat ulang token keamanan (CSRF) yang baru
        $request->session()->regenerateToken();

        // 4. Kembalikan ke halaman login
        return redirect('/login')->with('success', 'Anda berhasil keluar.');
    }
}