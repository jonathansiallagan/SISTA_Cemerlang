<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomposisiProduk extends Model
{
    protected $table = 'komposisi_produks';

    protected $fillable = [
        'produk_id',
        'bahan_baku_id',
        'jumlah_pemakaian',
        'satuan_pemakaian',
    ];

    protected $casts = [
        'jumlah_pemakaian' => 'decimal:4',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}