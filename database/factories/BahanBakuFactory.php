<?php

namespace Database\Factories;

use App\Models\BahanBaku;
use App\Models\Cabang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BahanBakuFactory extends Factory
{
    protected $model = BahanBaku::class;

    public function definition(): array
    {
        return [
            // Jika saat testing tidak diberi cabang_id, factory akan otomatis membuat Cabang dummy
            'cabang_id' => Cabang::factory(),

            // Generate kode unik, contoh: BB001, BB999
            'kode_bahan' => $this->faker->unique()->bothify('BB###'),

            'nama_bahan' => $this->faker->unique()->words(3, true),
            'spesifikasi' => $this->faker->sentence(),

            // Ambil satuan acak sesuai aturan validasi
            'satuan' => $this->faker->randomElement(['lembar', 'rim', 'pcs', 'm2', 'liter']),

            'stok' => $this->faker->randomFloat(3, 10, 1000),
            'harga_rata_rata' => $this->faker->randomFloat(2, 1000, 50000),
            'status' => 'aktif',
        ];
    }
}
