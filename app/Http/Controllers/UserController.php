<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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

    public function create()
    {
        $cabangs = Cabang::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.user.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in([
                    'Admin Pusat',
                    'Admin Cabang',
                    'Customer Service',
                ]),
            ],
            'cabang_id' => [
                'nullable',
                'required_if:role,Admin Cabang',
                'exists:cabangs,id',
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
            'cabang_id.required_if' =>
                'Admin Cabang wajib memiliki penempatan cabang.',
            'cabang_id.exists' => 'Cabang yang dipilih tidak valid.',
        ]);

        if ($validated['role'] === 'Admin Pusat') {
            $validated['cabang_id'] = null;
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'] ?? null,
        ]);

        return redirect()
            ->route('user.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
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
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in([
                    'Admin Pusat',
                    'Admin Cabang',
                    'Customer Service',
                ]),
            ],
            'cabang_id' => [
                'nullable',
                'required_if:role,Admin Cabang',
                'exists:cabangs,id',
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
            'cabang_id.required_if' =>
                'Admin Cabang wajib memiliki penempatan cabang.',
            'cabang_id.exists' => 'Cabang yang dipilih tidak valid.',
        ]);

        if ($validated['role'] === 'Admin Pusat') {
            $validated['cabang_id'] = null;
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'] ?? null,
        ];

        // Password hanya diperbarui jika pengguna mengisinya.
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('user.index')
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'Data pengguna berhasil dihapus!');
    }
}