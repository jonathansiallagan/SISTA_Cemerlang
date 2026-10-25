<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_jurnals', function (Blueprint $table) {
            $table->decimal('kuantitas', 15, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('detail_jurnals', function (Blueprint $table) {
            $table->dropColumn(['kuantitas', 'satuan', 'harga']);
        });
    }
};