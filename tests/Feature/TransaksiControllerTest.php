<?php

namespace Tests\Feature;

use App\Exports\TransaksiExport;
use App\Models\Akun;
use App\Models\BahanBaku;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\KomposisiProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorCabang = 0;
    private int $nomorTransaksi = 0;
    private int $nomorProduk = 0;
    private int $nomorBahan = 0;
    private int $nomorUser = 0;

    protected Cabang $cabangUtama;
    protected Cabang $cabangLain;
    protected User $adminPusat;
    protected User $adminCabang;
    protected User $customerService;
    protected Produk $produkLembar;
    protected BahanBaku $bahanLembar;
    protected Akun $akunKas;
    protected Akun $akunPendapatan;
    protected Akun $akunHpp;
    protected Akun $akunPersediaan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabangUtama = $this->buatCabang('Cabang Utama');
        $this->cabangLain = $this->buatCabang('Cabang Kedua');

        $this->adminPusat = $this->buatUser('Admin Pusat', $this->cabangUtama);
        $this->adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $this->customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->akunKas = $this->buatAkun(
            '1-1100',
            'Kas',
            'Aset',
            'Aset',
            'Debit'
        );

        $this->akunPendapatan = $this->buatAkun(
            '4-1100',
            'Pendapatan Penjualan',
            'Pendapatan',
            'Pendapatan Usaha',
            'Kredit'
        );

        $this->akunHpp = $this->buatAkun(
            '5-1100',
            'Harga Pokok Penjualan',
            'Beban/Biaya',
            'HPP',
            'Debit'
        );

        $this->akunPersediaan = $this->buatAkun(
            '1-1300',
            'Persediaan Bahan Baku',
            'Aset',
            'Persediaan',
            'Debit'
        );

        $this->bahanLembar = $this->buatBahanBaku([
            'nama_bahan' => 'Kertas Uji Transaksi',
            'stok' => 100,
            'harga_rata_rata' => 2000,
        ]);

        $this->produkLembar = $this->buatProduk([
            'nama_produk' => 'Cetak Brosur Uji',
            'satuan' => 'lembar',
            'upah_operator' => 500,
        ]);

        $this->buatKomposisi(
            $this->produkLembar,
            $this->bahanLembar,
            [
                'jumlah_pemakaian' => 1,
                'satuan_pemakaian' => 'lembar',
            ]
        );

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
        AKSES DAN DAFTAR TRANSAKSI
    ------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login_from_transaction_page(): void
    {
        Auth::logout();

        $this->get(route('transaksi.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_transaction_page(): void
    {
        $response = $this->get(route('transaksi.index'));

        $response->assertOk();
        $response->assertViewIs('admin.transaksi.index');
        $response->assertViewHasAll(['transaksis', 'cabangs']);
    }

    public function test_admin_cabang_can_open_transaction_page(): void
    {
        $this->actingAs($this->adminCabang)
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertViewIs('admin.transaksi.index');
    }

    public function test_customer_service_can_open_customer_service_transaction_page(): void
    {
        $this->actingAs($this->customerService)
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertViewIs('cs.transaksi.index');
    }

    public function test_index_displays_all_branches_for_admin_pusat(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-SEMUA-CABANG-01',
        ]);

        $this->buatTransaksi($this->cabangLain, $this->adminPusat, [
            'no_referensi' => 'INV-SEMUA-CABANG-02',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index'));

        $response->assertOk();

        $referensi = $this->referensiPadaResponse($response);

        $this->assertContains('INV-SEMUA-CABANG-01', $referensi);
        $this->assertContains('INV-SEMUA-CABANG-02', $referensi);
    }

    public function test_admin_pusat_can_filter_transactions_by_branch(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-FILTER-UTAMA',
        ]);

        $this->buatTransaksi($this->cabangLain, $this->adminPusat, [
            'no_referensi' => 'INV-FILTER-LAIN',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index', [
                'cabang_id' => $this->cabangUtama->id,
            ]));

        $response->assertOk();

        $this->assertSame(
            ['INV-FILTER-UTAMA'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_admin_cabang_only_sees_transactions_from_own_branch(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->adminCabang, [
            'no_referensi' => 'INV-CABANG-SENDIRI',
        ]);

        $this->buatTransaksi($this->cabangLain, $this->adminPusat, [
            'no_referensi' => 'INV-CABANG-LAIN',
        ]);

        $response = $this->actingAs($this->adminCabang)
            ->get(route('transaksi.index'));

        $response->assertOk();

        $this->assertSame(
            ['INV-CABANG-SENDIRI'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_customer_service_only_sees_transactions_from_own_branch(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-CS-SENDIRI',
        ]);

        $this->buatTransaksi($this->cabangLain, $this->adminPusat, [
            'no_referensi' => 'INV-CS-CABANG-LAIN',
        ]);

        $response = $this->actingAs($this->customerService)
            ->get(route('transaksi.index'));

        $response->assertOk();
        $response->assertViewIs('cs.transaksi.index');

        $this->assertSame(
            ['INV-CS-SENDIRI'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_customer_service_only_receives_its_own_branch_in_branch_filter(): void
    {
        $response = $this->actingAs($this->customerService)
            ->get(route('transaksi.index'));

        $response->assertOk();

        $response->assertViewHas('cabangs', function ($cabangs): bool {
            return $cabangs->count() === 1
                && $cabangs->first()->id === $this->cabangUtama->id;
        });
    }

    public function test_index_can_search_by_reference_number(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-CARI-REF-7788',
            'nama_pelanggan' => 'Pelanggan Alpha',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-REFERENSI-LAIN',
            'nama_pelanggan' => 'Pelanggan Beta',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index', [
                'search' => 'CARI-REF-7788',
            ]));

        $response->assertOk();

        $this->assertSame(
            ['INV-CARI-REF-7788'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_index_can_search_by_customer_name(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-PELANGGAN-01',
            'nama_pelanggan' => 'Pelanggan Mawar Biru',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-PELANGGAN-02',
            'nama_pelanggan' => 'Pelanggan Melati',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index', [
                'search' => 'Mawar Biru',
            ]));

        $response->assertOk();

        $this->assertSame(
            ['INV-PELANGGAN-01'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_index_can_filter_transactions_by_date_range(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-TANGGAL-AWAL',
            'tanggal' => '2026-10-01',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-TANGGAL-TENGAH',
            'tanggal' => '2026-10-15',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-TANGGAL-AKHIR',
            'tanggal' => '2026-11-01',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index', [
                'tanggal_mulai' => '2026-10-10',
                'tanggal_akhir' => '2026-10-20',
            ]));

        $response->assertOk();

        $this->assertSame(
            ['INV-TANGGAL-TENGAH'],
            $this->referensiPadaResponse($response)
        );
    }

    public function test_index_sorts_transactions_by_latest_date_and_latest_id(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-URUT-LAMA',
            'tanggal' => '2026-10-01',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-URUT-BARU-1',
            'tanggal' => '2026-11-01',
        ]);

        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-URUT-BARU-2',
            'tanggal' => '2026-11-01',
        ]);

        $response = $this->actingAs($this->adminPusat)
            ->get(route('transaksi.index'));

        $response->assertOk();

        $this->assertSame([
            'INV-URUT-BARU-2',
            'INV-URUT-BARU-1',
            'INV-URUT-LAMA',
        ], $this->referensiPadaResponse($response));
    }

    /* -----------------------------------------------------------------
     | FORM TRANSAKSI BARU
     |------------------------------------------------------------------ */

    public function test_customer_service_can_open_create_transaction_form(): void
    {
        $response = $this->actingAs($this->customerService)
            ->get(route('transaksi.create'));

        $response->assertOk();
        $response->assertViewIs('cs.transaksi.modal-page');
        $response->assertViewHasAll([
            'produks',
            'cabangs',
            'no_referensi',
        ]);

        $response->assertViewHas('cabangs', function ($cabangs): bool {
            return $cabangs->count() === 1
                && $cabangs->first()->id === $this->cabangUtama->id;
        });

        $response->assertViewHas('no_referensi', function ($noReferensi): bool {
            return preg_match('/^INV-\d{8}-\d{4}$/', $noReferensi) === 1;
        });
    }

    public function test_create_form_generates_next_reference_number_for_same_day(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-REFERENSI-HARI-INI',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->customerService)
            ->get(route('transaksi.create'));

        $response->assertOk();

        $response->assertViewHas('no_referensi', function ($noReferensi): bool {
            return str_ends_with($noReferensi, '-0002');
        });
    }

    public function test_admin_pusat_cannot_open_create_transaction_form(): void
    {
        $this->actingAs($this->adminPusat)
            ->get(route('transaksi.create'))
            ->assertForbidden();
    }

    public function test_admin_cabang_cannot_open_create_transaction_form(): void
    {
        $this->actingAs($this->adminCabang)
            ->get(route('transaksi.create'))
            ->assertForbidden();
    }

    /* -----------------------------------------------------------------
     | STORE TRANSAKSI: PROSES BERHASIL
     |------------------------------------------------------------------ */

    public function test_customer_service_can_store_transaction_and_update_stock(): void
    {
        $response = $this->actingAs($this->customerService)
            ->from(route('transaksi.create'))
            ->post(route('transaksi.store'), $this->dataTransaksiValid());

        $response->assertRedirect(route('transaksi.index'));
        $response->assertSessionHas(
            'success',
            'Transaksi, HPP, stok, dan jurnal berhasil disimpan!'
        );

        $transaksi = Transaksi::query()
            ->where('no_referensi', 'INV-UJI-0001')
            ->firstOrFail();

        $detail = $transaksi->detailTransaksis()->firstOrFail();

        $this->assertSame($this->cabangUtama->id, $transaksi->cabang_id);
        $this->assertSame($this->customerService->id, $transaksi->user_id);
        $this->assertSame('Pelanggan Uji', $transaksi->nama_pelanggan);

        $this->assertEquals(110000, (float) $transaksi->total_transaksi);
        $this->assertEquals(5000, (float) $transaksi->total_hpp);

        $this->assertEquals(98, (float) $this->bahanLembar->fresh()->stok);
        $this->assertEquals(100000, (float) $detail->harga_satuan);
        $this->assertEquals(110000, (float) $detail->subtotal);
        $this->assertEquals(5000, (float) $detail->total_hpp);
        $this->assertEquals(2500, (float) $detail->hpp_satuan);

        $this->assertSame(1, Transaksi::query()->count());
        $this->assertSame(1, DetailTransaksi::query()->count());
        $this->assertSame(1, Jurnal::query()->count());
        $this->assertSame(5, DetailJurnal::query()->count());
    }

    public function test_store_calculates_total_from_product_price_and_edit_service(): void
    {
        $payload = $this->dataTransaksiValid([
            'harga_jual' => [125000],
            'jasa_edit' => [15000],
        ]);

        $this->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::query()
            ->where('no_referensi', 'INV-UJI-0001')
            ->firstOrFail();

        $this->assertEquals(140000, (float) $transaksi->total_transaksi);

        $this->assertEquals(
            125000,
            (float) $transaksi->detailTransaksis()->first()->harga_satuan
        );

        $this->assertEquals(
            140000,
            (float) $transaksi->detailTransaksis()->first()->subtotal
        );
    }

    public function test_store_calculates_material_and_operator_cost_for_square_meter_product(): void
    {
        $bahan = $this->buatBahanBaku([
            'nama_bahan' => 'Vinyl Uji Meter',
            'stok' => 100,
            'harga_rata_rata' => 2500,
        ]);

        $produk = $this->buatProduk([
            'nama_produk' => 'Cetak Banner Uji',
            'satuan' => 'm2',
            'upah_operator' => 500,
        ]);

        $this->buatKomposisi($produk, $bahan, [
            'jumlah_pemakaian' => 0.5,
            'satuan_pemakaian' => 'm2',
        ]);

        $payload = $this->dataTransaksiValid($produk, [
            'no_referensi' => 'INV-METER-0001',
            'jumlah' => [2],
            'harga_jual' => [500000],
            'jasa_edit' => [0],
            'tinggi' => [2],
            'lebar' => [3],
        ]);

        $this->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::query()
            ->where('no_referensi', 'INV-METER-0001')
            ->firstOrFail();

        $detail = $transaksi->detailTransaksis()->firstOrFail();

        $this->assertEquals(12, (float) $detail->luas);
        $this->assertEquals(2, (float) $detail->tinggi);
        $this->assertEquals(3, (float) $detail->lebar);
        $this->assertEquals(94, (float) $bahan->fresh()->stok);
        $this->assertEquals(21000, (float) $transaksi->total_hpp);
    }

    public function test_store_can_save_multiple_products_in_one_transaction(): void
    {
        $bahanKedua = $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Uji Multi',
            'stok' => 100,
            'harga_rata_rata' => 1000,
        ]);

        $produkKedua = $this->buatProduk([
            'nama_produk' => 'Cetak Stiker Multi',
            'satuan' => 'lembar',
            'upah_operator' => 300,
        ]);

        $this->buatKomposisi($produkKedua, $bahanKedua, [
            'jumlah_pemakaian' => 2,
            'satuan_pemakaian' => 'lembar',
        ]);

        $payload = $this->dataTransaksiValid([
            'produk_id' => [
                $this->produkLembar->id,
                $produkKedua->id,
            ],
            'jumlah' => [2, 3],
            'harga_jual' => [100000, 75000],
            'jasa_edit' => [10000, 5000],
            'tinggi' => [null, null],
            'lebar' => [null, null],
        ]);

        $this->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertRedirect(route('transaksi.index'));

        $transaksi = Transaksi::query()
            ->where('no_referensi', 'INV-UJI-0001')
            ->firstOrFail();

        $this->assertSame(2, $transaksi->detailTransaksis()->count());
        $this->assertEquals(190000, (float) $transaksi->total_transaksi);
        $this->assertEquals(11900, (float) $transaksi->total_hpp);

        $this->assertEquals(98, (float) $this->bahanLembar->fresh()->stok);
        $this->assertEquals(94, (float) $bahanKedua->fresh()->stok);
    }

    public function test_store_creates_balanced_sales_journal(): void
    {
        $this->actingAs($this->customerService)
            ->post(route('transaksi.store'), $this->dataTransaksiValid())
            ->assertRedirect(route('transaksi.index'));

        $jurnal = Jurnal::query()
            ->where('no_referensi', 'INV-UJI-0001')
            ->firstOrFail();

        $details = $jurnal->detailJurnals;

        $this->assertCount(5, $details);

        $this->assertEquals(
            (float) $details->sum('debit'),
            (float) $details->sum('kredit')
        );

        $this->assertEquals(
            110000,
            (float) $details
                ->where('akun_id', $this->akunKas->id)
                ->where('debit', '>', 0)
                ->sum('debit')
        );

        $this->assertEquals(
            110000,
            (float) $details
                ->where('akun_id', $this->akunPendapatan->id)
                ->sum('kredit')
        );

        $this->assertEquals(
            5000,
            (float) $details
                ->where('akun_id', $this->akunHpp->id)
                ->sum('debit')
        );
    }

    /* -----------------------------------------------------------------
        STORE TRANSAKSI: VALIDASI INPUT
    ------------------------------------------------------------------ */

    public function test_guest_cannot_store_transaction(): void
    {
        Auth::logout();

        $this->post(route('transaksi.store'), [])
            ->assertRedirect(route('login'));
    }

    public function test_admin_pusat_cannot_store_transaction(): void
    {
        $this->actingAs($this->adminPusat)
            ->post(route('transaksi.store'), $this->dataTransaksiValid())
            ->assertForbidden();

        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_admin_cabang_cannot_store_transaction(): void
    {
        $this->actingAs($this->adminCabang)
            ->post(route('transaksi.store'), $this->dataTransaksiValid())
            ->assertForbidden();

        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_store_rejects_missing_reference_number(): void
    {
        $payload = $this->dataTransaksiValid();
        unset($payload['no_referensi']);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertRedirect(route('transaksi.create'))
            ->assertSessionHasErrors('no_referensi');
    }

    public function test_store_rejects_duplicate_reference_number(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-UJI-0001',
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $this->dataTransaksiValid())
            ->assertRedirect(route('transaksi.create'))
            ->assertSessionHasErrors('no_referensi');
    }

    public function test_store_rejects_missing_or_invalid_transaction_date(): void
    {
        $payload = $this->dataTransaksiValid();
        unset($payload['tanggal']);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('tanggal');

        $payload['tanggal'] = 'bukan-tanggal';

        $this->from(route('transaksi.create'))
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('tanggal');
    }

    public function test_store_rejects_missing_customer_name(): void
    {
        $payload = $this->dataTransaksiValid();
        unset($payload['nama_pelanggan']);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('nama_pelanggan');
    }

    public function test_store_rejects_customer_name_longer_than_255_characters(): void
    {
        $payload = $this->dataTransaksiValid([
            'nama_pelanggan' => str_repeat('A', 256),
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('nama_pelanggan');
    }

    public function test_store_rejects_phone_number_longer_than_20_characters(): void
    {
        $payload = $this->dataTransaksiValid([
            'nomor_hp' => str_repeat('1', 21),
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('nomor_hp');
    }

    public function test_store_rejects_missing_product_list(): void
    {
        $payload = $this->dataTransaksiValid();
        unset($payload['produk_id']);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('produk_id');
    }

    public function test_store_rejects_empty_product_list(): void
    {
        $payload = $this->dataTransaksiValid([
            'produk_id' => [],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('produk_id');
    }

    public function test_store_rejects_nonexistent_product_id(): void
    {
        $payload = $this->dataTransaksiValid([
            'produk_id' => [999999],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('produk_id.0');
    }

    public function test_store_rejects_zero_or_negative_quantity(): void
    {
        foreach ([0, -1] as $jumlah) {
            $payload = $this->dataTransaksiValid([
                'jumlah' => [$jumlah],
            ]);

            $this->from(route('transaksi.create'))
                ->actingAs($this->customerService)
                ->post(route('transaksi.store'), $payload)
                ->assertSessionHasErrors('jumlah.0');
        }
    }

    public function test_store_rejects_non_integer_quantity(): void
    {
        $payload = $this->dataTransaksiValid([
            'jumlah' => [1.5],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('jumlah.0');
    }

    public function test_store_rejects_negative_sale_price(): void
    {
        $payload = $this->dataTransaksiValid([
            'harga_jual' => [-1000],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('harga_jual.0');
    }

    public function test_store_rejects_negative_edit_service_fee(): void
    {
        $payload = $this->dataTransaksiValid([
            'jasa_edit' => [-1],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('jasa_edit.0');
    }

    public function test_store_rejects_missing_height_and_width_arrays(): void
    {
        $payload = $this->dataTransaksiValid();
        unset($payload['tinggi'], $payload['lebar']);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors(['tinggi', 'lebar']);
    }

    public function test_store_rejects_mismatched_product_detail_array_lengths(): void
    {
        $payload = $this->dataTransaksiValid([
            'jumlah' => [2, 3],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertRedirect(route('transaksi.create'))
            ->assertSessionHasErrors('jumlah');

        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_store_rejects_zero_height_or_width_for_square_meter_product(): void
    {
        $produk = $this->buatProduk([
            'nama_produk' => 'Banner Ukuran Nol',
            'satuan' => 'm2',
        ]);

        $this->buatKomposisi($produk, $this->bahanLembar);

        $payload = $this->dataTransaksiValid($produk, [
            'tinggi' => [0],
            'lebar' => [2],
        ]);

        $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload)
            ->assertSessionHasErrors('tinggi.0');
    }

    /* -----------------------------------------------------------------
     | STORE TRANSAKSI: ROLLBACK KETIKA PROSES GAGAL
     |------------------------------------------------------------------ */

    public function test_store_rolls_back_when_material_stock_is_insufficient(): void
    {
        $this->bahanLembar->update([
            'stok' => 1,
        ]);

        $response = $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $this->dataTransaksiValid());

        $response->assertRedirect(route('transaksi.create'));
        $response->assertSessionHas('error');

        $this->assertSame(0, Transaksi::query()->count());
        $this->assertSame(0, DetailTransaksi::query()->count());
        $this->assertSame(0, Jurnal::query()->count());
        $this->assertEquals(1, (float) $this->bahanLembar->fresh()->stok);
    }

    public function test_store_rolls_back_when_product_has_no_material_composition(): void
    {
        $produkTanpaKomposisi = $this->buatProduk([
            'nama_produk' => 'Produk Tanpa Komposisi',
        ]);

        $payload = $this->dataTransaksiValid($produkTanpaKomposisi, [
            'no_referensi' => 'INV-TANPA-KOMPOSISI',
        ]);

        $response = $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $payload);

        $response->assertRedirect(route('transaksi.create'));
        $response->assertSessionHas('error');

        $this->assertSame(0, Transaksi::query()->count());
        $this->assertSame(0, DetailTransaksi::query()->count());
        $this->assertSame(0, Jurnal::query()->count());
    }

    public function test_store_rolls_back_when_required_account_is_missing_or_inactive(): void
    {
        $this->akunKas->update([
            'status' => 'nonaktif',
        ]);

        $response = $this->from(route('transaksi.create'))
            ->actingAs($this->customerService)
            ->post(route('transaksi.store'), $this->dataTransaksiValid());

        $response->assertRedirect(route('transaksi.create'));
        $response->assertSessionHas('error');

        $this->assertSame(0, Transaksi::query()->count());
        $this->assertSame(0, Jurnal::query()->count());
        $this->assertEquals(100, (float) $this->bahanLembar->fresh()->stok);
    }

    /* -----------------------------------------------------------------
     | EXPORT PDF DAN EXCEL
     |------------------------------------------------------------------ */

    public function test_pdf_export_returns_download_response(): void
    {
        $this->buatTransaksi($this->cabangUtama, $this->customerService, [
            'no_referensi' => 'INV-EXPORT-PDF',
        ]);

        $response = $this->get(route('transaksi.export.pdf'));

        $response->assertOk();
        $response->assertDownload();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_excel_export_downloads_transaksi_export(): void
    {
        Excel::fake();

        $namaFile = 'riwayat-transaksi-' . now()->format('Ymd-His') . '.xlsx';

        $response = $this->get(route('transaksi.export.excel'));

        $response->assertOk();

        Excel::assertDownloaded($namaFile, function ($export): bool {
            return $export instanceof TransaksiExport;
        });
    }

    public function test_excel_export_respects_admin_cabang_branch_scope(): void
    {
        Excel::fake();

        $target = $this->buatTransaksi($this->cabangUtama, $this->adminCabang, [
            'no_referensi' => 'INV-EXCEL-CABANG-SENDIRI',
        ]);

        $this->buatTransaksi($this->cabangLain, $this->adminPusat, [
            'no_referensi' => 'INV-EXCEL-CABANG-LAIN',
        ]);

        $namaFile = 'riwayat-transaksi-' . now()->format('Ymd-His') . '.xlsx';

        $response = $this->actingAs($this->adminCabang)
            ->get(route('transaksi.export.excel'));

        $response->assertOk();

        Excel::assertDownloaded($namaFile, function ($export) use ($target): bool {
            return $export instanceof TransaksiExport
                && $export->collection()->pluck('id')->all() === [$target->id];
        });
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function referensiPadaResponse($response): array
    {
        $transaksis = collect($response->viewData('transaksis'));

        return $transaksis
            ->map(fn($transaksi) => data_get($transaksi, 'no_referensi'))
            ->all();
    }

    private function buatCabang(?string $nama = null): Cabang
    {
        $nomor = ++$this->nomorCabang;

        return Cabang::create([
            'nama' => $nama ?? 'Cabang Transaksi Uji ' . $nomor,
            'alamat' => 'Alamat Cabang Transaksi Uji ' . $nomor,
            'no_hp' => '08123456' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
        ]);
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $nomor = ++$this->nomorUser;
        $slugRole = strtolower(str_replace(' ', '-', $role));

        return User::factory()->create([
            'name' => 'User Transaksi Uji ' . $nomor,
            'username' => 'user-transaksi-' . $slugRole . '-' . $nomor,
            'email' => 'user-transaksi-' . $nomor . '@example.test',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatAkun(
        string $kode,
        string $nama,
        string $tipe,
        string $kelompok,
        string $saldoNormal
    ): Akun {
        return Akun::create([
            'kode_akun' => $kode,
            'nama_akun' => $nama,
            'tipe_akun' => $tipe,
            'kelompok_akun' => $kelompok,
            'saldo_normal' => $saldoNormal,
        ]);
    }

    private function buatBahanBaku(array $attributes = []): BahanBaku
    {
        $nomor = ++$this->nomorBahan;

        return BahanBaku::create(array_merge([
            'cabang_id' => $this->cabangUtama->id,
            'kode_bahan' => 'BB-TRX-' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
            'nama_bahan' => 'Bahan Transaksi Uji ' . $nomor,
            'spesifikasi' => 'Spesifikasi bahan transaksi uji',
            'satuan' => 'lembar',
            'stok' => 100,
            'harga_rata_rata' => 2000,
        ], $attributes));
    }

    private function buatProduk(array $attributes = []): Produk
    {
        $nomor = ++$this->nomorProduk;

        return Produk::create(array_merge([
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->adminPusat->id,
            'nama_produk' => 'Produk Transaksi Uji ' . $nomor,
            'satuan' => 'lembar',
            'upah_operator' => 500,
        ], $attributes));
    }

    private function buatKomposisi(
        Produk $produk,
        BahanBaku $bahan,
        array $attributes = []
    ): KomposisiProduk {
        return KomposisiProduk::create(array_merge([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah_pemakaian' => 1,
            'satuan_pemakaian' => $bahan->satuan,
        ], $attributes));
    }

    private function buatTransaksi(
        Cabang $cabang,
        User $user,
        array $attributes = []
    ): Transaksi {
        $nomor = ++$this->nomorTransaksi;

        $transaksi = Transaksi::create(array_merge([
            'no_referensi' => 'INV-TRX-UJI-' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'nama_pelanggan' => 'Pelanggan Transaksi Uji ' . $nomor,
            'nomor_hp' => '081234567890',
            'cabang_id' => $cabang->id,
            'user_id' => $user->id,
            'total_transaksi' => 100000,
            'total_hpp' => 5000,
        ], $attributes));

        return $transaksi->fresh();
    }

    private function dataTransaksiValid(
        Produk|array|null $produk = null,
        array $attributes = []
    ): array {
        if (is_array($produk)) {
            $attributes = $produk;
            $produk = null;
        }

        $produk ??= $this->produkLembar;

        return array_merge([
            'no_referensi' => 'INV-UJI-0001',
            'tanggal' => now()->toDateString(),
            'nama_pelanggan' => 'Pelanggan Uji',
            'nomor_hp' => '081234567890',
            'produk_id' => [$produk->id],
            'jumlah' => [2],
            'harga_jual' => [100000],
            'jasa_edit' => [10000],
            'tinggi' => [null],
            'lebar' => [null],
        ], $attributes);
    }
}
