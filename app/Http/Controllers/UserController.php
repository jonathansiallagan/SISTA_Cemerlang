<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan seluruh pengguna beserta cabang masing-masing.
     */
    public function index()
    {
        $users = User::with('cabang')
            ->orderBy('name')
            ->get();

        $cabangs = Cabang::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.user.index', compact('users', 'cabangs'));
    }

    /**
     * Menambahkan pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username'),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                Rule::in([
                    'Admin Pusat',
                    'Admin Cabang',
                    'Customer Service',
                ]),
            ],

            'cabang_id' => [
                'required',
                'integer',
                Rule::exists('cabangs', 'id')
                    ->where('status', 'aktif'),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',

            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',

            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',

            'cabang_id.required' => 'Setiap pengguna wajib memiliki penempatan cabang.',
            'cabang_id.integer' => 'Penempatan cabang tidak valid.',
            'cabang_id.exists' => 'Cabang yang dipilih tidak ditemukan atau tidak aktif.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'],
        ]);

        return redirect()
            ->route('user.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                Rule::in([
                    'Admin Pusat',
                    'Admin Cabang',
                    'Customer Service',
                ]),
            ],

            'cabang_id' => [
                'required',
                'integer',
                Rule::exists('cabangs', 'id')
                    ->where('status', 'aktif'),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',

            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',

            'password.min' => 'Password minimal 8 karakter.',

            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',

            'cabang_id.required' => 'Setiap pengguna wajib memiliki penempatan cabang.',
            'cabang_id.integer' => 'Penempatan cabang tidak valid.',
            'cabang_id.exists' => 'Cabang yang dipilih tidak ditemukan atau tidak aktif.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'],
        ];

        // Password hanya diperbarui jika diisi.
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('user.index')
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'Data pengguna berhasil dihapus!');
    }
}