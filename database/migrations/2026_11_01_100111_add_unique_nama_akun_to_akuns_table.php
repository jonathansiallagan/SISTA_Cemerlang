<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            $table->unique('nama_akun', 'akuns_nama_akun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            $table->dropUnique('akuns_nama_akun_unique');
        });
    }
};