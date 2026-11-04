<?php

namespace App\Support;

final class PerhitunganSista
{
    // Menghitung total luas pesanan produk dalam satuan m².
    public static function hitungLuasPesanan(
        float $tinggi,
        float $lebar,
        int $jumlah
    ): float {
        return $tinggi * $lebar * $jumlah;
    }

    // Produk m² menggunakan luas, produk lain menggunakan jumlah barang.
    public static function hitungPengaliPemakaian(
        bool $isM2,
        float $luas,
        int $jumlah
    ): float {
        return $isM2 ? $luas : (float) $jumlah;
    }

    // Menghitung jumlah bahan yang digunakan.
    public static function hitungJumlahPemakaian(
        float $pemakaianPerSatuan,
        float $pengaliPemakaian
    ): float {
        return $pemakaianPerSatuan * $pengaliPemakaian;
    }

    // Menghitung biaya bahan berdasarkan harga rata-rata.
    public static function hitungBiayaBahan(
        float $jumlahPemakaian,
        float $hargaRataRata
    ): float {
        return $jumlahPemakaian * $hargaRataRata;
    }

    // Menghitung upah operator.
    public static function hitungUpahOperator(
        float $upahPerSatuan,
        float $pengaliPemakaian
    ): float {
        return $upahPerSatuan * $pengaliPemakaian;
    }

    // HPP produk = biaya bahan + upah operator.
    public static function hitungHppProduk(
        float $hppBahan,
        float $upahOperator
    ): float {
        return $hppBahan + $upahOperator;
    }

    // Subtotal = harga barang + jasa edit.
    public static function hitungSubtotal(
        float $hargaFinalBarang,
        float $jasaEdit
    ): float {
        return $hargaFinalBarang + $jasaEdit;
    }

    // Menjumlahkan beberapa nilai.
    public static function hitungTotal(array $nilai): float
    {
        return array_sum(array_map('floatval', $nilai));
    }

    // Menghitung harga rata-rata tertimbang setelah pembelian.
    public static function hitungHargaRataRataBaru(
        float $stokLama,
        float $hargaRataRataLama,
        float $jumlahPembelian,
        float $hargaPembelian
    ): float {
        $stokBaru = $stokLama + $jumlahPembelian;

        if ($stokBaru <= 0) {
            return 0.0;
        }

        $nilaiPersediaanLama = $stokLama * $hargaRataRataLama;
        $nilaiPembelianBaru = $jumlahPembelian * $hargaPembelian;

        return round(
            ($nilaiPersediaanLama + $nilaiPembelianBaru) / $stokBaru,
            2
        );
    }

    // Menghitung stok setelah pemakaian.
    public static function hitungStokAkhir(
        float $stokAwal,
        float $jumlahPemakaian
    ): float {
        return $stokAwal - $jumlahPemakaian;
    }

    // Menghitung nilai persediaan.
    public static function hitungNilaiPersediaan(
        float $stok,
        float $hargaRataRata
    ): float {
        return $stok * $hargaRataRata;
    }

    // Saldo normal pendapatan adalah kredit.
    public static function hitungSaldoPendapatan(
        float $debit,
        float $kredit
    ): float {
        return $kredit - $debit;
    }

    // Saldo normal HPP dan beban adalah debit.
    public static function hitungSaldoBeban(
        float $debit,
        float $kredit
    ): float {
        return $debit - $kredit;
    }

    // Menghitung laba kotor dan laba bersih.
    public static function hitungLabaRugi(
        float $totalPendapatan,
        float $totalHpp,
        float $totalBeban
    ): array {
        $labaKotor = $totalPendapatan - $totalHpp;
        $labaBersih = $labaKotor - $totalBeban;

        return [
            'labaKotor' => $labaKotor,
            'labaBersih' => $labaBersih,
        ];
    }
}