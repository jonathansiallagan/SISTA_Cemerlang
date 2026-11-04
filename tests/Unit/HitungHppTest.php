<?php

namespace Tests\Unit;

use App\Support\PerhitunganSista;
use PHPUnit\Framework\TestCase;

class HitungHppTest extends TestCase
{
    public function test_hpp_bahan_dihitung_dari_pemakaian_dan_harga_rata_rata(): void
    {
        $jumlahPemakaian = PerhitunganSista::hitungJumlahPemakaian(
            0.5,
            4
        );

        $biayaBahan = PerhitunganSista::hitungBiayaBahan(
            $jumlahPemakaian,
            20000
        );

        $this->assertEquals(2.0, $jumlahPemakaian);
        $this->assertEquals(40000.0, $biayaBahan);
    }

    public function test_hpp_produk_merupakan_penjumlahan_biaya_bahan_dan_upah_operator(): void
    {
        $hppBahan = 40000;

        $upahOperator = PerhitunganSista::hitungUpahOperator(
            3000,
            4
        );

        $hppProduk = PerhitunganSista::hitungHppProduk(
            $hppBahan,
            $upahOperator
        );

        $this->assertEquals(12000.0, $upahOperator);
        $this->assertEquals(52000.0, $hppProduk);
    }

    public function test_hpp_produk_meter_persegi_menggunakan_total_luas_pesanan(): void
    {
        $luas = PerhitunganSista::hitungLuasPesanan(
            2,
            3,
            2
        );

        $pengali = PerhitunganSista::hitungPengaliPemakaian(
            true,
            $luas,
            2
        );

        $jumlahPemakaian = PerhitunganSista::hitungJumlahPemakaian(
            0.1,
            $pengali
        );

        $biayaBahan = PerhitunganSista::hitungBiayaBahan(
            $jumlahPemakaian,
            50000
        );

        $upah = PerhitunganSista::hitungUpahOperator(
            2500,
            $pengali
        );

        $hppProduk = PerhitunganSista::hitungHppProduk(
            $biayaBahan,
            $upah
        );

        $this->assertEquals(12.0, $luas);
        $this->assertEqualsWithDelta(
            1.2,
            $jumlahPemakaian,
            0.000001
        );
        $this->assertEqualsWithDelta(60000.0, $biayaBahan, 0.000001);
        $this->assertEqualsWithDelta(30000.0, $upah, 0.000001);
        $this->assertEqualsWithDelta(90000.0, $hppProduk, 0.000001);
    }

    public function test_hpp_dapat_menjumlahkan_biaya_beberapa_bahan(): void
    {
        $biayaBahan = [
            PerhitunganSista::hitungBiayaBahan(2, 10000),
            PerhitunganSista::hitungBiayaBahan(1.5, 20000),
            PerhitunganSista::hitungBiayaBahan(0.5, 40000),
        ];

        $totalHppBahan = PerhitunganSista::hitungTotal(
            $biayaBahan
        );

        $this->assertEquals(70000.0, $totalHppBahan);
    }
}