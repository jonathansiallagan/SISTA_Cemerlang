<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_referensi')->unique(); // Nama kolom sudah final
            $table->date('tanggal');
            $table->string('nama_pelanggan');
            $table->string('nomor_hp', 20)->nullable(); // Kolom tambahan dimasukkan di sini
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_transaksi', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
