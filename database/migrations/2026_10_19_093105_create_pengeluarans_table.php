<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();

            $table->string('no_referensi')->unique();
            $table->date('tanggal');

            $table->foreignId('cabang_id')
                ->constrained('cabangs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Kolom akun_id digabungkan ke sini
            $table->foreignId('akun_id')
                ->nullable()
                ->constrained('akuns')
                ->restrictOnDelete();

            $table->text('keterangan');

            // Kolom qty, satuan, dan harga digabungkan ke sini
            $table->decimal('qty', 12, 2)->default(1);
            $table->string('satuan', 30)->default('Unit');
            $table->decimal('harga', 15, 2)->default(0);

            // Sebaiknya diberi nilai default 0 agar mencegah error saat insert awal
            $table->decimal('total_pengeluaran', 15, 2)->default(0);

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
