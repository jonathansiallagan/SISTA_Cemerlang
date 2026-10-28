<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama_produk', 'satuan'])]
class Produk extends Model
{
    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}