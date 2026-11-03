<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke detail transaksi.
     */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    /**
     * Relasi ke komposisi produk.
     */
    public function komposisi(): HasMany
    {
        return $this->hasMany(KomposisiProduk::class, 'produk_id');
    }

    /**
     * Relasi ke cabang pemilik produk.
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Relasi ke user yang membuat produk.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}