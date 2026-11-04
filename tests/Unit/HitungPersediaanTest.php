<?php

namespace Tests\Unit;

use App\Support\PerhitunganSista;
use PHPUnit\Framework\TestCase;

class HitungPersediaanTest extends TestCase
{
    public function test_harga_rata_rata_tertimbang_setelah_pembelian(): void
    {
        // Stok lama: 10 unit dengan harga Rp10.000.
        // Pembelian: 5 unit dengan harga Rp16.000.

        $hargaRataRata = PerhitunganSista::hitungHargaRataRataBaru(
            10,
            10000,
            5,
            16000
        );

        $this->assertEquals(12000.0, $hargaRataRata);
    }

    public function test_harga_rata_rata_saat_stok_awal_kosong(): void
    {
        $hargaRataRata = PerhitunganSista::hitungHargaRataRataBaru(
            0,
            0,
            8,
            15000
        );

        $this->assertEquals(15000.0, $hargaRataRata);
    }

    public function test_stok_akhir_dihitung_setelah_pemakaian_bahan(): void
    {
        $stokAkhir = PerhitunganSista::hitungStokAkhir(
            20,
            3.5
        );

        $this->assertEquals(16.5, $stokAkhir);
    }

    public function test_nilai_persediaan_dihitung_dari_stok_dan_harga_rata_rata(): void
    {
        $nilaiPersediaan = PerhitunganSista::hitungNilaiPersediaan(
            16.5,
            12000
        );

        $this->assertEquals(198000.0, $nilaiPersediaan);
    }
}