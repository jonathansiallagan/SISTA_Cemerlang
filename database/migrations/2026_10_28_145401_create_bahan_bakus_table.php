<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan', 30)->unique();
            $table->string('nama_bahan', 150);
            $table->string('spesifikasi', 150)->nullable();
            $table->string('satuan', 30);

            $table->decimal('stok', 15, 3)->default(0);
            $table->decimal('harga_rata_rata', 15, 2)->default(0);

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};