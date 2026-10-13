<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        // Mengambil data transaksi beserta relasi user dan cabangnya
        $query = Transaksi::with(['user', 'cabang'])->latest();

        // Customer Service hanya boleh melihat transaksi cabangnya sendiri
        if (Auth::user()->isCustomerService()) {
            $query->where('cabang_id', Auth::user()->cabang_id);
        }

        $transaksis = $query->get();
        return view('admin.transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $produks = Produk::all();
        $cabangs = Cabang::all();

        // Customer Service hanya boleh memilih cabangnya sendiri
        if (Auth::user()->isCustomerService()) {
            $cabangs = $cabangs->where('id', Auth::user()->cabang_id)->values();
        }
        
        // Pembuatan Nomor Invoice Otomatis
        $today = now()->format('Ymd');
        $lastTransaksi = Transaksi::whereDate('created_at', now()->today())->count();
        $no_invoice = 'INV-' . $today . '-' . str_pad($lastTransaksi + 1, 4, '0', STR_PAD_LEFT);

        return view('admin.transaksi.create', compact('produks', 'cabangs', 'no_invoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_invoice' => 'required|unique:transaksis,no_invoice',
            'tanggal' => 'required|date',
            'nama_pelanggan' => 'required|string|max:255',
            'cabang_id' => 'required|exists:cabangs,id',
            'produk_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        // Customer Service tidak boleh menyimpan transaksi untuk cabang lain
        if (Auth::user()->isCustomerService() && (int) $request->cabang_id !== (int) Auth::user()->cabang_id) {
            abort(403, 'Anda hanya dapat mencatat transaksi untuk cabang Anda sendiri.');
        }
        DB::beginTransaction();
        try {
            // 1. Simpan tabel induk (Transaksi)
            $transaksi = Transaksi::create([
                'no_invoice' => $request->no_invoice,
                'tanggal' => $request->tanggal,
                'nama_pelanggan' => $request->nama_pelanggan,
                'cabang_id' => $request->cabang_id,
                'user_id' => Auth::id(), 
                'total_transaksi' => $request->total_transaksi_input,
                'total_hpp' => $request->total_hpp_input,
            ]);

            // 2. Simpan tabel anak (Detail Transaksi) berulang sesuai jumlah produk
            foreach ($request->produk_id as $key => $produkId) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produkId,
                    'jumlah' => $request->jumlah[$key],
                    'lebar' => $request->lebar[$key] ?? null,
                    'tinggi' => $request->tinggi[$key] ?? null,
                    'luas' => $request->luas[$key] ?? null,
                    'harga_satuan' => $request->harga_satuan[$key],
                    'hpp_satuan' => $request->hpp_satuan[$key],
                    'subtotal' => $request->subtotal[$key],
                    'total_hpp' => $request->total_hpp_item[$key],
                ]);
            }

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}