<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Pengeluaran::with(['cabang', 'user'])
            ->latest('tanggal')
            ->latest('id');

        if ($user->role === 'Admin Cabang') {
            $query->where('cabang_id', $user->cabang_id);
        }

        $pengeluarans = $query->get();

        return view('admin.pengeluaran.index', compact('pengeluarans'));
    }

    public function create()
    {
        $user = Auth::user();

        $cabangs = Cabang::where('status', 'aktif')
            ->when(
                $user->role === 'Admin Cabang',
                fn ($query) => $query->where('id', $user->cabang_id)
            )
            ->get();

        return view('admin.pengeluaran.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'cabang_id' => ['required', 'exists:cabangs,id'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'total_pengeluaran' => [
                'required',
                'numeric',
                'min:1',
                'max:9999999999999.99',
            ],
        ]);

        if ($user->role === 'Admin Cabang') {
            $data['cabang_id'] = $user->cabang_id;
        }

        $tanggal = str_replace('-', '', $data['tanggal']);

        $referensiTerakhir = Pengeluaran::where(
            'no_referensi',
            'like',
            'EXP-' . $tanggal . '-%'
        )
            ->lockForUpdate()
            ->get()
            ->map(fn ($item) => (int) substr($item->no_referensi, -4))
            ->max() ?? 0;

        $data['no_referensi'] = sprintf(
            'EXP-%s-%04d',
            $tanggal,
            $referensiTerakhir + 1
        );

        $data['user_id'] = $user->id;

        Pengeluaran::create($data);

        return redirect()
            ->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }
}
