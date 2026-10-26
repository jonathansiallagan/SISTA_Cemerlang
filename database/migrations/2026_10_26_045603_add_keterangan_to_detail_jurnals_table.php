<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_jurnals', function (Blueprint $table) {
            $table->string('keterangan')->nullable()->after('akun_id');
        });
    }

    public function down(): void
    {
        Schema::table('detail_jurnals', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};