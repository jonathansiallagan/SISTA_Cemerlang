<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;

class CabangController extends Controller
{
    public function index()
    {
        // Tampilkan cabang aktif dan nonaktif
        $cabangs = Cabang::all();

        return view('admin.cabang.index', compact('cabangs'));
    }

    public function create()
    {
        return view('admin.cabang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
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
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ]);

        $cabang->update($validated);

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $cabang = Cabang::findOrFail($id);

        $statusBaru = $cabang->status === 'aktif'
            ? 'nonaktif'
            : 'aktif';

        $cabang->update([
            'status' => $statusBaru,
        ]);

        return redirect()
            ->route('cabang.index')
            ->with(
                'success',
                'Status cabang berhasil diubah menjadi ' . ucfirst($statusBaru) . '.'
            );
    }
}