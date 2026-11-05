<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = [
        'cabang_id', // Tambahan
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

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function detailPembelian(): HasMany
    {
        return $this->hasMany(
            DetailPembelianBahan::class,
            'bahan_baku_id'
        );
    }
}
