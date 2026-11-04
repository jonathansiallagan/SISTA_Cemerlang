<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('restrict');
            $table->integer('jumlah');
            $table->decimal('lebar', 8, 2)->nullable();
            $table->decimal('tinggi', 8, 2)->nullable();
            $table->decimal('luas', 8, 2)->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('hpp_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('jasa_edit', 15, 2)->default(0); // Kolom tambahan dimasukkan di sini
            $table->decimal('total_hpp', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
