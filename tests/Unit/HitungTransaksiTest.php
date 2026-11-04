<?php

namespace Tests\Unit;

use App\Support\PerhitunganSista;
use PHPUnit\Framework\TestCase;

class HitungTransaksiTest extends TestCase
{
    public function test_subtotal_transaksi_mencakup_harga_barang_dan_jasa_edit(): void
    {
        $subtotal = PerhitunganSista::hitungSubtotal(
            500000,
            50000
        );

        $this->assertEquals(550000.0, $subtotal);
    }

    public function test_luas_total_dihitung_dari_tinggi_lebar_dan_jumlah_barang(): void
    {
        $luas = PerhitunganSista::hitungLuasPesanan(
            1.5,
            2,
            3
        );

        $this->assertEquals(9.0, $luas);
    }

    public function test_produk_m2_menggunakan_luas_sebagai_pengali_pemakaian(): void
    {
        $pengali = PerhitunganSista::hitungPengaliPemakaian(
            true,
            9,
            3
        );

        $this->assertEquals(9.0, $pengali);
    }

    public function test_produk_non_m2_menggunakan_jumlah_barang_sebagai_pengali(): void
    {
        $pengali = PerhitunganSista::hitungPengaliPemakaian(
            false,
            9,
            3
        );

        $this->assertEquals(3.0, $pengali);
    }

    public function test_total_transaksi_merupakan_jumlah_seluruh_subtotal(): void
    {
        $subtotalBaris = [
            PerhitunganSista::hitungSubtotal(500000, 50000),
            PerhitunganSista::hitungSubtotal(250000, 25000),
            PerhitunganSista::hitungSubtotal(100000, 0),
        ];

        $totalTransaksi = PerhitunganSista::hitungTotal(
            $subtotalBaris
        );

        $this->assertEquals(925000.0, $totalTransaksi);
    }

    public function test_total_hpp_transaksi_merupakan_jumlah_hpp_setiap_produk(): void
    {
        $hppProduk = [
            PerhitunganSista::hitungHppProduk(40000, 12000),
            PerhitunganSista::hitungHppProduk(30000, 8000),
        ];

        $totalHpp = PerhitunganSista::hitungTotal(
            $hppProduk
        );

        $this->assertEquals(90000.0, $totalHpp);
    }
}