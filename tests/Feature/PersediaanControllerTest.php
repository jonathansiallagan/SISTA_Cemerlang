<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\Cabang;
use App\Models\DetailPembelianBahan;
use App\Models\PembelianBahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PersediaanControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorBahan = 0;

    private int $nomorUser = 0;

    protected User $adminPusat;
    protected Cabang $cabangUtama;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabangUtama = Cabang::factory()->create();

        $this->adminPusat = $this->buatUser('Admin Pusat', $this->cabangUtama);

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
        AKSES DAN INDEX
    ------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        Auth::logout();

        $response = $this->get(route('persediaan.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_inventory_page(): void
    {
        $response = $this->get(route('persediaan.index'));

        $response->assertOk();
        $response->assertViewIs('admin.persediaan.index');
        $response->assertViewHasAll([
            'bahanBakus',
            'totalJenisBahan',
            'totalStok',
            'totalNilaiPersediaan',
        ]);
    }

    public function test_admin_cabang_can_open_inventory_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)
            ->get(route('persediaan.index'));

        $response->assertOk();
        $response->assertViewIs('admin.persediaan.index');
    }

    public function test_customer_service_cannot_access_inventory_page(): void
    {
        $customerService = $this->buatUser('Customer Service');

        $response = $this->actingAs($customerService)
            ->get(route('persediaan.index'));

        $response->assertForbidden();
    }

    /* -----------------------------------------------------------------
        INDEX: DAFTAR PERSEDIAAN
    ------------------------------------------------------------------ */

    public function test_index_displays_inventory_sorted_by_material_name(): void
    {
        $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Printer',
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Flexi Banner',
        ]);

        $response = $this->get(route('persediaan.index'));

        $response->assertOk();
        $response->assertViewHas(
            'bahanBakus',
            function ($bahanBakus): bool {
                return $bahanBakus->pluck('nama_bahan')->all() === [
                    'Flexi Banner',
                    'Kertas A4',
                    'Tinta Printer',
                ];
            }
        );
    }

    public function test_index_calculates_total_material_types_correctly(): void
    {
        $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Printer',
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Flexi Banner',
        ]);

        $response = $this->get(route('persediaan.index'));
        $response->assertOk();
        $response->assertViewHas('totalJenisBahan', 3);
    }

    public function test_index_calculates_total_stock_correctly(): void
    {
        $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
            'stok' => 10,
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Printer',
            'stok' => 5,
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Flexi Banner',
            'stok' => 2.5,
        ]);

        $response = $this->get(route('persediaan.index'));

        $response->assertOk();
        $response->assertViewHas(
            'totalStok',
            fn($total) => (float) $total === 17.5
        );
    }

    public function test_index_calculates_total_inventory_value_correctly(): void
    {
        $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
            'stok' => 10,
            'harga_rata_rata' => 10000,
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Printer',
            'stok' => 5,
            'harga_rata_rata' => 20000,
        ]);

        $response = $this->get(route('persediaan.index'));

        $response->assertOk();

        // (10 x 10.000) + (5 x 20.000) = 200.000

        $response->assertViewHas(
            'totalNilaiPersediaan',
            fn($total) => (float) $total === 200000.0
        );
    }

    public function test_index_returns_zero_summary_when_inventory_is_empty(): void
    {
        $response = $this->get(route('persediaan.index'));

        $response->assertOk();
        $response->assertViewHas(
            'bahanBakus',
            fn($items) => $items->isEmpty()
        );
        $response->assertViewHas('totalJenisBahan', 0);
        $response->assertViewHas('totalStok', 0);
        $response->assertViewHas('totalNilaiPersediaan', 0);
    }

    /* -----------------------------------------------------------------
        STORE: PEMBELIAN PERSEDIAAN
    ------------------------------------------------------------------ */

    public function test_admin_pusat_can_store_purchase_and_update_stock(): void
    {
        $bahan = $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
            'stok' => 10,
            'harga_rata_rata' => 10000,
        ]);

        $tanggal = '2026-11-04';

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => $tanggal,
                'nama_pemasok' => 'PT Pemasok Kertas',
                'keterangan' => 'Pembelian kertas A4',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 5,
                        'harga_satuan' => 15000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHas(
            'success',
            'Pembelian berhasil disimpan. Stok dan harga rata-rata telah diperbarui.'
        );

        // Stok awal 10 + pembelian 5 = 15.

        $bahan->refresh();

        $this->assertEquals(15, (float) $bahan->stok);

        // Harga rata-rata tertimbang:

        // ((10 x 10.000) + (5 x 15.000)) / 15

        // = 11.666,67.

        $this->assertEquals(
            11666.67,
            (float) $bahan->harga_rata_rata
        );
        $this->assertSame(1, PembelianBahan::query()->count());
        $this->assertSame(1, DetailPembelianBahan::query()->count());

        $pembelian = PembelianBahan::firstOrFail();

        $this->assertSame(
            'PT Pemasok Kertas',
            $pembelian->nama_pemasok
        );
        $this->assertEquals(
            75000,
            (float) $pembelian->total_harga
        );
        $this->assertDatabaseHas((new DetailPembelianBahan())->getTable(), [
            'pembelian_bahan_id' => $pembelian->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 5,
            'harga_satuan' => 15000,
            'total_harga' => 75000,
        ]);
    }

    public function test_admin_cabang_can_store_inventory_purchase(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $bahan = $this->buatBahanBaku([
            'stok' => 10,
            'harga_rata_rata' => 5000,
        ]);

        $response = $this->actingAs($adminCabang)
            ->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'nama_pemasok' => 'Pemasok Cabang',
                'keterangan' => 'Pembelian oleh admin cabang',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 5,
                        'harga_satuan' => 6000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasNoErrors();

        $bahan->refresh();

        $this->assertEquals(15, (float) $bahan->stok);
        $this->assertSame(1, PembelianBahan::query()->count());
    }

    public function test_store_generates_purchase_number_automatically(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'items' => [
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => 2,
                    'harga_satuan' => 10000,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        $pembelian = PembelianBahan::firstOrFail();

        $this->assertMatchesRegularExpression(
            '/^PB-\d{14}-[A-Z0-9]{4}$/',
            $pembelian->no_pembelian
        );
    }

    public function test_store_calculates_purchase_total_from_all_items(): void
    {
        $bahanPertama = $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
            'stok' => 0,
        ]);

        $bahanKedua = $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Printer',
            'stok' => 0,
        ]);

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'nama_pemasok' => 'PT Pemasok',
            'keterangan' => 'Pembelian beberapa bahan',
            'total_harga' => 1,
            'items' => [
                [
                    'bahan_baku_id' => $bahanPertama->id,
                    'jumlah' => 10,
                    'harga_satuan' => 5000,
                ],
                [
                    'bahan_baku_id' => $bahanKedua->id,
                    'jumlah' => 5,
                    'harga_satuan' => 20000,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        // (10 x 5.000) + (5 x 20.000) = 150.000.

        $pembelian = PembelianBahan::firstOrFail();

        $this->assertEquals(
            150000,
            (float) $pembelian->total_harga
        );
        $this->assertSame(2, DetailPembelianBahan::query()->count());

        $bahanPertama->refresh();

        $bahanKedua->refresh();

        $this->assertEquals(10, (float) $bahanPertama->stok);
        $this->assertEquals(5, (float) $bahanKedua->stok);
    }

    public function test_store_updates_weighted_average_price_correctly(): void
    {
        $bahan = $this->buatBahanBaku([
            'stok' => 20,
            'harga_rata_rata' => 10000,
        ]);

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'items' => [
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => 10,
                    'harga_satuan' => 20000,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        $bahan->refresh();

        // ((20 x 10.000) + (10 x 20.000)) / 30

        // = 13.333,33.

        $this->assertEquals(30, (float) $bahan->stok);
        $this->assertEquals(
            13333.33,
            (float) $bahan->harga_rata_rata
        );
    }

    public function test_store_sets_average_price_when_initial_stock_is_zero(): void
    {
        $bahan = $this->buatBahanBaku([
            'stok' => 0,
            'harga_rata_rata' => 0,
        ]);

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'items' => [
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => 4,
                    'harga_satuan' => 2500,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        $bahan->refresh();

        $this->assertEquals(4, (float) $bahan->stok);
        $this->assertEquals(2500, (float) $bahan->harga_rata_rata);
    }

    public function test_store_accepts_zero_purchase_price(): void
    {
        $bahan = $this->buatBahanBaku([
            'stok' => 10,
            'harga_rata_rata' => 10000,
        ]);

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'items' => [
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => 5,
                    'harga_satuan' => 0,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        $bahan->refresh();

        // ((10 x 10.000) + (5 x 0)) / 15

        // = 6.666,67.

        $this->assertEquals(15, (float) $bahan->stok);
        $this->assertEquals(
            6666.67,
            (float) $bahan->harga_rata_rata
        );
    }

    public function test_store_accepts_optional_supplier_and_description_as_null(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->post(route('persediaan.store'), [
            'tanggal_pembelian' => '2026-11-04',
            'items' => [
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => 2,
                    'harga_satuan' => 5000,
                ],
            ],
        ]);

        $response->assertRedirect(route('persediaan.index'));

        $pembelian = PembelianBahan::firstOrFail();

        $this->assertNull($pembelian->nama_pemasok);
        $this->assertNull($pembelian->keterangan);
    }

    /* -----------------------------------------------------------------
        VALIDASI STORE
    ------------------------------------------------------------------ */

    public function test_store_rejects_missing_purchase_date(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('tanggal_pembelian');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_invalid_purchase_date(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => 'bukan-tanggal',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('tanggal_pembelian');
    }

    public function test_store_rejects_missing_items(): void
    {
        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_empty_items_array(): void
    {
        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_missing_material_id(): void
    {
        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.bahan_baku_id');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_nonexistent_material_id(): void
    {
        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => 999999,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.bahan_baku_id');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_duplicate_material_in_multiple_items(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 3,
                        'harga_satuan' => 6000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.1.bahan_baku_id');

        $this->assertSame(0, PembelianBahan::query()->count());
        $this->assertSame(0, DetailPembelianBahan::query()->count());
    }

    public function test_store_rejects_missing_quantity(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.jumlah');
    }

    public function test_store_rejects_zero_quantity(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 0,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.jumlah');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_negative_quantity(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => -5,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.jumlah');
    }

    public function test_store_rejects_non_numeric_quantity(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 'dua',
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.jumlah');
    }

    public function test_store_rejects_missing_unit_price(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.harga_satuan');
    }

    public function test_store_rejects_negative_unit_price(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => -5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.harga_satuan');

        $this->assertSame(0, PembelianBahan::query()->count());
    }

    public function test_store_rejects_non_numeric_unit_price(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 'sepuluh ribu',
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('items.0.harga_satuan');
    }

    public function test_store_rejects_supplier_name_longer_than_255_characters(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'nama_pemasok' => str_repeat('A', 256),
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('nama_pemasok');
    }

    public function test_store_rejects_description_longer_than_1000_characters(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('persediaan.index'))
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'keterangan' => str_repeat('A', 1001),
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertRedirect(route('persediaan.index'));
        $response->assertSessionHasErrors('keterangan');
    }

    /* -----------------------------------------------------------------
        PENANGANAN BAHAN BAKU NONAKTIF DAN ROLLBACK
    ------------------------------------------------------------------ */

    public function test_customer_service_cannot_store_inventory_purchase(): void
    {
        $customerService = $this->buatUser('Customer Service');

        $bahan = $this->buatBahanBaku();

        $response = $this->actingAs($customerService)
            ->post(route('persediaan.store'), [
                'tanggal_pembelian' => '2026-11-04',
                'items' => [
                    [
                        'bahan_baku_id' => $bahan->id,
                        'jumlah' => 2,
                        'harga_satuan' => 5000,
                    ],
                ],
            ]);

        $response->assertForbidden();

        $this->assertSame(0, PembelianBahan::query()->count());
        $this->assertSame(0, DetailPembelianBahan::query()->count());

        $bahan->refresh();

        $this->assertEquals(12.5, (float) $bahan->stok);
    }

    /* -----------------------------------------------------------------
        HELPER DATA TEST
    ------------------------------------------------------------------ */

    private function buatBahanBaku(array $attributes = []): BahanBaku
    {
        $this->nomorBahan++;

        return BahanBaku::create(array_merge([
            'cabang_id' => $this->cabangUtama->id,
            'kode_bahan' => 'BB' . str_pad(
                (string) $this->nomorBahan,
                3,
                '0',
                STR_PAD_LEFT
            ),
            'nama_bahan' => 'Bahan Uji ' . $this->nomorBahan,
            'spesifikasi' => 'Spesifikasi bahan uji',
            'satuan' => 'pcs',
            'stok' => 12.500,
            'harga_rata_rata' => 2500,
        ], $attributes));
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $this->nomorUser++;

        return User::factory()->create([
            'name' => $role . ' Pengujian ' . $this->nomorUser,
            'username' => strtolower(str_replace(' ', '_', $role)) . '_persediaan_' . $this->nomorUser,
            'email' => 'persediaan-user-' . $this->nomorUser . '@example.test',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }
}
