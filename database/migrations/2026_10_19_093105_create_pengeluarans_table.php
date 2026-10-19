
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

            $table->text('keterangan');
            $table->decimal('total_pengeluaran', 15, 2);

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
