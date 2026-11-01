<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->unique('kode_bahan', 'bahan_baku_kode_unique');
            $table->unique('nama_bahan', 'bahan_baku_nama_unique');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropUnique('bahan_baku_kode_unique');
            $table->dropUnique('bahan_baku_nama_unique');
        });
    }
};