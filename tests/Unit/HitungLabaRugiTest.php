<?php

namespace Tests\Unit;

use App\Support\PerhitunganSista;
use PHPUnit\Framework\TestCase;

class HitungLabaRugiTest extends TestCase
{
    public function test_saldo_akun_pendapatan_menggunakan_kredit_dikurangi_debit(): void
    {
        $saldoPendapatan = PerhitunganSista::hitungSaldoPendapatan(
            500000,
            10000000
        );

        $this->assertEquals(9500000.0, $saldoPendapatan);
    }

    public function test_saldo_akun_hpp_dan_beban_menggunakan_debit_dikurangi_kredit(): void
    {
        $saldoBeban = PerhitunganSista::hitungSaldoBeban(
            3000000,
            250000
        );

        $this->assertEquals(2750000.0, $saldoBeban);
    }

    public function test_laporan_laba_rugi_menghitung_laba_kotor_dan_laba_bersih(): void
    {
        $hasil = PerhitunganSista::hitungLabaRugi(
            10000000,
            3000000,
            2000000
        );

        $this->assertEquals(7000000.0, $hasil['labaKotor']);
        $this->assertEquals(5000000.0, $hasil['labaBersih']);
    }

    public function test_laporan_menunjukkan_rugi_jika_beban_melebihi_laba_kotor(): void
    {
        $hasil = PerhitunganSista::hitungLabaRugi(
            3000000,
            2000000,
            2000000
        );

        $this->assertEquals(1000000.0, $hasil['labaKotor']);
        $this->assertEquals(-1000000.0, $hasil['labaBersih']);
    }
}