<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['no_referensi', 'tanggal', 'keterangan', 'cabang_id', 'user_id'])]
class Jurnal extends Model
{
    public function cabang() { return $this->belongsTo(Cabang::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function detailJurnals() { return $this->hasMany(DetailJurnal::class); }
    public function transaksi()
{
    return $this->belongsTo(Transaksi::class, 'no_referensi', 'no_referensi');
}
    public function pengeluaran()
{
    return $this->hasOne(Pengeluaran::class, 'no_referensi', 'no_referensi');
}
}
