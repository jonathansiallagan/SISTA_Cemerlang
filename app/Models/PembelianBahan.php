<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembelianBahan extends Model
{
    protected $table = 'pembelian_bahan';

    protected $fillable = [
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

    public function detail(): HasMany
    {
        return $this->hasMany(
            DetailPembelianBahan::class,
            'pembelian_bahan_id'
        );
    }
}