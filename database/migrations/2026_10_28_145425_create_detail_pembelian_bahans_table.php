<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pembelian_bahan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pembelian_bahan_id')
                ->constrained('pembelian_bahan')
                ->restrictOnDelete();

            $table->foreignId('bahan_baku_id')
                ->constrained('bahan_baku')
                ->restrictOnDelete();

            $table->decimal('jumlah', 15, 3);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian_bahan');
    }
};