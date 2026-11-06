<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Bahan Baku
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();

            // Relasi ke cabang
            $table->foreignId('cabang_id')->constrained('cabangs')->restrictOnDelete();

            $table->string('kode_bahan', 30);
            $table->string('nama_bahan', 150);
            $table->string('spesifikasi', 150)->nullable();
            $table->string('satuan', 30);

            $table->decimal('stok', 15, 3)->default(0);
            $table->decimal('harga_rata_rata', 15, 2)->default(0);

            $table->timestamps();

            // Unik per cabang: Tidak boleh ada kode atau nama yang sama di dalam satu cabang yang sama
            $table->unique(['cabang_id', 'kode_bahan'], 'bahan_kode_cabang_unique');
            $table->unique(['cabang_id', 'nama_bahan'], 'bahan_nama_cabang_unique');
        });

        // 2. Tabel Pembelian Bahan
        Schema::create('pembelian_bahan', function (Blueprint $table) {
            $table->id();

            // Relasi ke cabang
            $table->foreignId('cabang_id')->constrained('cabangs')->restrictOnDelete();

            $table->string('no_pembelian', 40);
            $table->date('tanggal_pembelian');
            $table->string('nama_pemasok', 150)->nullable();
            $table->text('keterangan')->nullable();

            $table->decimal('total_harga', 15, 2)->default(0);

            $table->timestamps();

            // Nomor pembelian dipastikan unik per cabang
            $table->unique(['cabang_id', 'no_pembelian'], 'pembelian_no_cabang_unique');
        });

        // 3. Tabel Detail Pembelian Bahan
        Schema::create('detail_pembelian_bahan', function (Blueprint $table) {
            $table->id();

            // Jika nota pembelian dihapus, detail barang yang dibeli juga otomatis terhapus (cascade)
            $table->foreignId('pembelian_bahan_id')
                ->constrained('pembelian_bahan')
                ->cascadeOnDelete();

            // Tidak boleh menghapus bahan baku jika sudah pernah ada riwayat pembelian (restrict)
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
        // Drop tabel harus dari anak (detail) ke induk (master) agar tidak error foreign key
        Schema::dropIfExists('detail_pembelian_bahan');
        Schema::dropIfExists('pembelian_bahan');
        Schema::dropIfExists('bahan_baku');
    }
};
