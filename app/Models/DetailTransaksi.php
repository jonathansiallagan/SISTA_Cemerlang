<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable(['transaksi_id', 'produk_id', 'jumlah', 'lebar', 'tinggi', 'luas', 'harga_satuan', 'hpp_satuan', 'subtotal', 'total_hpp'])]
class DetailTransaksi extends Model
{
    public function transaksi() { return $this->belongsTo(Transaksi::class); }
    public function produk() { return $this->belongsTo(Produk::class); }
}