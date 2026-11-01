<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom tambahan dari percobaan migration sebelumnya
        if (Schema::hasColumn('cabangs', 'nama_normalisasi')) {
            Schema::table('cabangs', function (Blueprint $table) {
                $table->dropColumn('nama_normalisasi');
            });
        }

        // Jadikan kolom nama sebagai UNIQUE
        Schema::table('cabangs', function (Blueprint $table) {
            $table->unique('nama', 'cabangs_nama_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropUnique('cabangs_nama_unique');
        });
    }
};