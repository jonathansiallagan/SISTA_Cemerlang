<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;
use Illuminate\Validation\Rule;

class CabangController extends Controller
{
    public function index()
    {
        // Tampilkan semua cabang, baik aktif maupun nonaktif.
        $cabangs = Cabang::orderBy('nama')->get();

        return view('admin.cabang.index', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_hp' => ['required', 'string', 'max:20'],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ], [
            'nama.required' => 'Nama cabang wajib diisi.',
            'nama.max' => 'Nama cabang maksimal 255 karakter.',
            'alamat.required' => 'Alamat cabang wajib diisi.',
            'alamat.max' => 'Alamat maksimal 1000 karakter.',
            'no_hp.required' => 'Nomor HP/telepon wajib diisi.',
            'no_hp.max' => 'Nomor HP/telepon maksimal 20 karakter.',
            'status.required' => 'Status cabang wajib dipilih.',
            'status.in' => 'Status cabang tidak valid.',
        ]);

        Cabang::create($validated);

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Data cabang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $cabang = Cabang::findOrFail($id);

        return view('admin.cabang.edit', compact('cabang'));
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_hp' => ['required', 'string', 'max:20'],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ], [
            'nama.required' => 'Nama cabang wajib diisi.',
            'nama.max' => 'Nama cabang maksimal 255 karakter.',
            'alamat.required' => 'Alamat cabang wajib diisi.',
            'alamat.max' => 'Alamat maksimal 1000 karakter.',
            'no_hp.required' => 'Nomor HP/telepon wajib diisi.',
            'no_hp.max' => 'Nomor HP/telepon maksimal 20 karakter.',
            'status.required' => 'Status cabang wajib dipilih.',
            'status.in' => 'Status cabang tidak valid.',
        ]);

        $cabang->update($validated);

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $cabang = Cabang::findOrFail($id);

        $statusBaru = strtolower($cabang->status) === 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $cabang->update([
            'status' => $statusBaru,
        ]);

        return redirect()
            ->route('cabang.index')
            ->with(
                'success',
                'Status cabang berhasil diubah menjadi ' .
                ucfirst($statusBaru) . '.'
            );
    }
}