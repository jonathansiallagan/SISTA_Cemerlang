<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPembelianBahan extends Model
{
    protected $table = 'detail_pembelian_bahan';

    protected $fillable = [
        'pembelian_bahan_id',
        'bahan_baku_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
            'harga_satuan' => 'decimal:2',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(
            PembelianBahan::class,
            'pembelian_bahan_id'
        );
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(
            BahanBaku::class,
            'bahan_baku_id'
        );
    }
}