<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'spesifikasi',
        'satuan',
        'stok',
        'harga_rata_rata',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:3',
            'harga_rata_rata' => 'decimal:2',
        ];
    }

    public function detailPembelian(): HasMany
    {
        return $this->hasMany(
            \App\Models\DetailPembelianBahan::class,
            'bahan_baku_id'
        );
    }
}