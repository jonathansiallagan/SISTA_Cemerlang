<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'jurnal_id',
    'akun_id',
    'keterangan',
    'quantity',
    'satuan',
    'harga',
    'total_harga',
    'debit',
    'kredit'
])]
class DetailJurnal extends Model
{
    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class);
    }
}