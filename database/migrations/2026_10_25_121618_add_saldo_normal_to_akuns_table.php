
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            $table->enum('saldo_normal', ['Debit', 'Kredit'])
                ->default('Debit')
                ->after('kelompok_akun');
        });

        DB::table('akuns')
            ->whereIn('tipe_akun', ['Kewajiban', 'Ekuitas', 'Pendapatan'])
            ->update(['saldo_normal' => 'Kredit']);

        DB::table('akuns')
            ->whereIn('tipe_akun', ['Aset', 'Beban', 'Beban/Biaya', 'Biaya'])
            ->update(['saldo_normal' => 'Debit']);
    }

    public function down(): void
    {
        Schema::table('akuns', function (Blueprint $table) {
            $table->dropColumn('saldo_normal');
        });
    }
};
