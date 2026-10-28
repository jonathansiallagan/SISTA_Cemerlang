
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komposisi_produks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produk_id')
                ->constrained('produks')
                ->cascadeOnDelete();

            $table->foreignId('bahan_baku_id')
                ->constrained('bahan_baku')
                ->restrictOnDelete();

            $table->decimal('jumlah_pemakaian', 12, 4);
            $table->string('satuan_pemakaian', 30);

            $table->timestamps();

            $table->unique(
                ['produk_id', 'bahan_baku_id'],
                'komposisi_produk_bahan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komposisi_produks');
    }
};
