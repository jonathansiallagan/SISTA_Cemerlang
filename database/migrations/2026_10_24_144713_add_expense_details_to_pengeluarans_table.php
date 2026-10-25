
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->foreignId('akun_id')
                ->nullable()
                ->constrained('akuns')
                ->restrictOnDelete();

            $table->decimal('qty', 12, 2)->default(1);
            $table->string('satuan', 30)->default('Unit');
            $table->decimal('harga', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['akun_id']);
            $table->dropColumn([
                'akun_id',
                'qty',
                'satuan',
                'harga',
            ]);
        });
    }
};
