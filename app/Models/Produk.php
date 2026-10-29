<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_produk', 'satuan', 'upah_operator'])]
class Produk extends Model
{
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function komposisi(): HasMany
    {
        return $this->hasMany(KomposisiProduk::class, 'produk_id');
    }
}