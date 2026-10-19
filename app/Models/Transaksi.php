<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['no_referensi', 'tanggal', 'nama_pelanggan', 'cabang_id', 'user_id', 'total_transaksi', 'total_hpp'])]
class Transaksi extends Model
{
    public function cabang() { return $this->belongsTo(Cabang::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function detailTransaksis() { return $this->hasMany(DetailTransaksi::class); }
}