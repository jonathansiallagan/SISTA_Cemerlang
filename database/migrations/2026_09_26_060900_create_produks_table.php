<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();

            // Relasi digabungkan dari file revisi
            $table->foreignId('cabang_id')
                ->nullable()
                ->constrained('cabangs')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            // Kolom dasar
            $table->string('nama_produk');
            $table->string('satuan');

            // Kolom upah ditambahkan, harga_jual dan hpp dihilangkan
            $table->decimal('upah_operator', 12, 2)->default(0);

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
