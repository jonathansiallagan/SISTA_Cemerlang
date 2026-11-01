
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // Relasi ke tabel cabangs
            $table->foreignId('cabang_id')
                ->nullable()
                ->after('id')
                ->constrained('cabangs')
                ->restrictOnDelete();

            // Relasi ke tabel users
            $table->foreignId('user_id')
                ->nullable()
                ->after('cabang_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Composite Unique Key
            $table->unique(
                ['cabang_id', 'nama_produk'],
                'produk_cabang_nama_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropUnique('produk_cabang_nama_unique');

            $table->dropForeign(['user_id']);
            $table->dropForeign(['cabang_id']);

            $table->dropColumn(['user_id', 'cabang_id']);
        });
    }
};
