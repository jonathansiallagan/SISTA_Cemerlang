<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profil.index');
    }

    // FUNGSI UBAH PROFIL
    public function updateProfile(Request $request)
    {
        $user = $this->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, 
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return back()->with('success', 'Profil dasar berhasil diperbarui!');
    }

    // FUNGSI UBAH AVATAR (UPLOAD VIA AJAX)
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $this->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        // Mengembalikan respons JSON agar ditangkap oleh AJAX
        return response()->json([
            'success' => true,
            'avatar_url' => asset('storage/' . $path)
        ]);
    }

    // FUNGSI UBAH PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            // current_password otomatis memverifikasi kecocokan hash password lama
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $this->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah demi keamanan!');
    }

    // FUNGSI SIMPAN PREFERENSI TEMA (JSON)
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system',
        ]);

        $user = $this->user();
        $prefs = $user->preferences ?? [];

        // Tambahkan atau timpa kunci 'theme'
        $prefs['theme'] = $request->theme;

        // Simpan kembali array tersebut (Laravel otomatis mengubahnya menjadi JSON)
        $user->preferences = $prefs;
        $user->save();

        return back()->with('success', 'Preferensi tampilan berhasil disimpan!');
    }
}