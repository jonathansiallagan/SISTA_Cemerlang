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

    // 1. FUNGSI UBAH PROFIL & UPLOAD AVATAR
    public function updateProfile(Request $request)
    {
        $user = $this->user();

        $request->validate([
            'name' => 'required|string|max:255',
            // Pengecualian email unik untuk user yang sedang login
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Logika Upload Gambar
        if ($request->hasFile('avatar')) {
            // Hapus foto lama dari penyimpanan jika sebelumnya sudah ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke folder 'storage/app/public/avatars'
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Profil dan avatar berhasil diperbarui!');
    }

    // 2. FUNGSI UBAH PASSWORD
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

    // 3. FUNGSI SIMPAN PREFERENSI TEMA (JSON)
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