<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianBahan extends Model
{
    protected $table = 'pembelian_bahan';

    protected $fillable = [
        'cabang_id', // Tambahan
        'no_pembelian',
        'tanggal_pembelian',
        'nama_pemasok',
        'keterangan',
        'total_harga',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pembelian' => 'date',
            'total_harga' => 'decimal:2',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(
            DetailPembelianBahan::class,
            'pembelian_bahan_id'
        );
    }
}
