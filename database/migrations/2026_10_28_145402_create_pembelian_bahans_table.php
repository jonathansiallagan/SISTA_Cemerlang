<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_bahan', function (Blueprint $table) {
            $table->id();

            $table->string('no_pembelian', 40)->unique();
            $table->date('tanggal_pembelian');
            $table->string('nama_pemasok', 150)->nullable();
            $table->text('keterangan')->nullable();

            $table->decimal('total_harga', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_bahan');
    }
};