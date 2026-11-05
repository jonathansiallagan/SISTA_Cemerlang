<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\Cabang;
use App\Models\DetailTransaksi;
use App\Models\KomposisiProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProdukControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorCabang = 0;
    private int $nomorUser = 0;
    private int $nomorBahan = 0;
    private int $nomorProduk = 0;
    private int $nomorTransaksi = 0;

    protected Cabang $cabangUtama;
    protected User $adminPusat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabangUtama = $this->buatCabang();
        $this->adminPusat = $this->buatUser('Admin Pusat', $this->cabangUtama);

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
        AKSES DAN INDEX
    ------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        Auth::logout();

        $this->get(route('produk.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_product_page(): void
    {
        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewIs('admin.produk.index');
        $response->assertViewHasAll(['produks', 'bahanBakus', 'status']);
    }

    public function test_admin_cabang_can_open_product_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $this->actingAs($adminCabang)
            ->get(route('produk.index'))
            ->assertOk()
            ->assertViewIs('admin.produk.index');
    }

    public function test_customer_service_cannot_access_product_page(): void
    {
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->actingAs($customerService)
            ->get(route('produk.index'))
            ->assertForbidden();
    }

    public function test_index_displays_only_products_owned_by_logged_in_branch(): void
    {
        $cabangLain = $this->buatCabang();
        $produkSendiri = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Cabang Sendiri',
        ]);
        $this->buatProduk($cabangLain, [
            'nama_produk' => 'Produk Cabang Lain',
        ]);

        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('produks', function ($produks) use ($produkSendiri): bool {
            return $produks->count() === 1
                && $produks->first()->id === $produkSendiri->id;
        });
        $response->assertSee('Produk Cabang Sendiri');
        $response->assertDontSee('Produk Cabang Lain');
    }

    public function test_index_defaults_to_active_products(): void
    {
        $aktif = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Aktif Uji',
            'status' => 'aktif',
        ]);
        $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Nonaktif Uji',
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('status', 'aktif');
        $response->assertViewHas('produks', function ($produks) use ($aktif): bool {
            return $produks->count() === 1
                && $produks->first()->id === $aktif->id;
        });
    }

    public function test_index_can_display_nonactive_products_when_status_is_selected(): void
    {
        $nonaktif = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Nonaktif Pilihan',
            'status' => 'nonaktif',
        ]);
        $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Aktif Pilihan',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('produk.index', ['status' => 'nonaktif']));

        $response->assertOk();
        $response->assertViewHas('status', 'nonaktif');
        $response->assertViewHas('produks', function ($produks) use ($nonaktif): bool {
            return $produks->count() === 1
                && $produks->first()->id === $nonaktif->id;
        });
    }

    public function test_index_displays_products_sorted_by_name(): void
    {
        $this->buatProduk($this->cabangUtama, ['nama_produk' => 'Zebra Print']);
        $this->buatProduk($this->cabangUtama, ['nama_produk' => 'Banner']);
        $this->buatProduk($this->cabangUtama, ['nama_produk' => 'Cetak Brosur']);

        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('produks', function ($produks): bool {
            return $produks->pluck('nama_produk')->all() === [
                'Banner',
                'Cetak Brosur',
                'Zebra Print',
            ];
        });
    }

    public function test_index_displays_only_active_materials_sorted_by_name(): void
    {
        $aktifZ = $this->buatBahanBaku([
            'nama_bahan' => 'Tinta Z',
            'status' => 'aktif',
        ]);
        $aktifA = $this->buatBahanBaku([
            'nama_bahan' => 'Kertas A4',
            'status' => 'aktif',
        ]);
        $this->buatBahanBaku([
            'nama_bahan' => 'Kertas Nonaktif',
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('bahanBakus', function ($bahanBakus) use ($aktifA, $aktifZ): bool {
            return $bahanBakus->pluck('id')->all() === [$aktifA->id, $aktifZ->id];
        });
    }

    public function test_index_loads_product_compositions_and_transaction_count(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $bahan = $this->buatBahanBaku();
        $this->buatKomposisi($produk, $bahan);

        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('produks', function ($produks) use ($produk): bool {
            $hasil = $produks->firstWhere('id', $produk->id);

            return $hasil !== null
                && $hasil->relationLoaded('komposisi')
                && $hasil->komposisi->count() === 1
                && (int) $hasil->detail_transaksis_count === 0;
        });
    }

    public function test_index_returns_empty_product_list_when_branch_has_no_products(): void
    {
        $response = $this->get(route('produk.index'));

        $response->assertOk();
        $response->assertViewHas('produks', fn ($produks): bool => $produks->isEmpty());
    }

    public function test_index_returns_forbidden_when_user_has_no_branch(): void
    {
        $userTanpaCabang = $this->buatUser('Admin Pusat');

        $this->actingAs($userTanpaCabang)
            ->get(route('produk.index'))
            ->assertForbidden();
    }

    /* -----------------------------------------------------------------
     | STORE: TAMBAH PRODUK DAN KOMPOSISI
     |------------------------------------------------------------------ */

    public function test_admin_pusat_can_store_product_with_multiple_compositions(): void
    {
        $bahanPertama = $this->buatBahanBaku(['nama_bahan' => 'Kertas A4']);
        $bahanKedua = $this->buatBahanBaku(['nama_bahan' => 'Tinta Printer']);

        $data = [
            'nama_produk' => 'Cetak Brosur A4',
            'satuan' => 'lembar',
            'upah_operator' => 2500,
            'bahan_baku_id' => [$bahanPertama->id, $bahanKedua->id],
            'jumlah_pemakaian' => [2.5, 0.25],
            'satuan_pemakaian' => ['lembar', 'ml'],
        ];

        $response = $this->post(route('produk.store'), $data);

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHas('success');

        $produk = Produk::query()->where('nama_produk', 'Cetak Brosur A4')->firstOrFail();

        $this->assertSame($this->cabangUtama->id, $produk->cabang_id);
        $this->assertSame($this->adminPusat->id, $produk->user_id);
        $this->assertSame('lembar', $produk->satuan);
        $this->assertEquals(2500, (float) $produk->upah_operator);
        $this->assertSame('aktif', $produk->status);
        $this->assertSame(2, $produk->komposisi()->count());

        $this->assertDatabaseHas('komposisi_produks', [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanPertama->id,
            'jumlah_pemakaian' => 2.5,
            'satuan_pemakaian' => 'lembar',
        ]);
        $this->assertDatabaseHas('komposisi_produks', [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanKedua->id,
            'jumlah_pemakaian' => 0.25,
            'satuan_pemakaian' => 'ml',
        ]);
    }

    public function test_admin_cabang_can_store_product_for_its_own_branch(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $bahan = $this->buatBahanBaku();

        $response = $this->actingAs($adminCabang)
            ->post(route('produk.store'), $this->dataProdukValid([
                'nama_produk' => 'Produk Admin Cabang',
                'bahan_baku_id' => [$bahan->id],
            ]));

        $response->assertRedirect(route('produk.index'));
        $produk = Produk::query()->where('nama_produk', 'Produk Admin Cabang')->firstOrFail();

        $this->assertSame($this->cabangUtama->id, $produk->cabang_id);
        $this->assertSame($adminCabang->id, $produk->user_id);
    }

    public function test_store_allows_same_product_name_in_different_branches(): void
    {
        $cabangLain = $this->buatCabang();
        $this->buatProduk($cabangLain, ['nama_produk' => 'Cetak Kalender']);
        $bahan = $this->buatBahanBaku();

        $response = $this->post(route('produk.store'), $this->dataProdukValid([
            'nama_produk' => 'Cetak Kalender',
            'bahan_baku_id' => [$bahan->id],
        ]));

        $response->assertRedirect(route('produk.index'));
        $this->assertSame(
            1,
            Produk::query()
                ->where('cabang_id', $this->cabangUtama->id)
                ->where('nama_produk', 'Cetak Kalender')
                ->count()
        );
    }

    public function test_store_rejects_duplicate_product_name_in_same_branch(): void
    {
        $this->buatProduk($this->cabangUtama, ['nama_produk' => 'Cetak Kalender']);

        $response = $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'nama_produk' => 'Cetak Kalender',
            ]));

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHasErrors('nama_produk');
        $this->assertSame(1, Produk::query()->count());
    }

    public function test_store_accepts_zero_operator_wage(): void
    {
        $response = $this->post(route('produk.store'), $this->dataProdukValid([
            'upah_operator' => 0,
        ]));

        $response->assertRedirect(route('produk.index'));
        $this->assertEquals(
            0,
            (float) Produk::query()->firstOrFail()->upah_operator
        );
    }

    public function test_store_rejects_missing_product_name(): void
    {
        $data = $this->dataProdukValid();
        unset($data['nama_produk']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('nama_produk');

        $this->assertSame(0, Produk::query()->count());
    }

    public function test_store_rejects_product_name_longer_than_255_characters(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'nama_produk' => str_repeat('A', 256),
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('nama_produk');
    }

    public function test_store_rejects_non_string_product_name(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'nama_produk' => ['Nama Produk'],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('nama_produk');
    }

    public function test_store_rejects_missing_unit(): void
    {
        $data = $this->dataProdukValid();
        unset($data['satuan']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('satuan');
    }

    public function test_store_rejects_unit_longer_than_50_characters(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'satuan' => str_repeat('a', 51),
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('satuan');
    }

    public function test_store_rejects_missing_operator_wage(): void
    {
        $data = $this->dataProdukValid();
        unset($data['upah_operator']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('upah_operator');
    }

    public function test_store_rejects_non_numeric_operator_wage(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'upah_operator' => 'bukan angka',
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('upah_operator');
    }

    public function test_store_rejects_negative_operator_wage(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'upah_operator' => -1,
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('upah_operator');
    }

    public function test_store_rejects_missing_material_list(): void
    {
        $data = $this->dataProdukValid();
        unset($data['bahan_baku_id']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_store_rejects_empty_material_list(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'bahan_baku_id' => [],
                'jumlah_pemakaian' => [],
                'satuan_pemakaian' => [],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_store_rejects_material_list_that_is_not_an_array(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'bahan_baku_id' => '1',
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_store_rejects_missing_material_id_in_composition(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'bahan_baku_id' => [null],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id.0');
    }

    public function test_store_rejects_nonexistent_material_id(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'bahan_baku_id' => [999999],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id.0');
    }

    public function test_store_rejects_duplicate_material_ids(): void
    {
        $bahan = $this->buatBahanBaku();

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'bahan_baku_id' => [$bahan->id, $bahan->id],
                'jumlah_pemakaian' => [1, 2],
                'satuan_pemakaian' => ['lembar', 'lembar'],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('bahan_baku_id.1');
    }

    public function test_store_rejects_missing_material_usage_list(): void
    {
        $data = $this->dataProdukValid();
        unset($data['jumlah_pemakaian']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('jumlah_pemakaian');
    }

    public function test_store_rejects_zero_material_usage(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'jumlah_pemakaian' => [0],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('jumlah_pemakaian.0');
    }

    public function test_store_rejects_non_numeric_material_usage(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'jumlah_pemakaian' => ['banyak'],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('jumlah_pemakaian.0');
    }

    public function test_store_rejects_material_usage_list_with_different_size(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'jumlah_pemakaian' => [1, 2],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('jumlah_pemakaian');
    }

    public function test_store_rejects_missing_material_usage_unit_list(): void
    {
        $data = $this->dataProdukValid();
        unset($data['satuan_pemakaian']);

        $this->from(route('produk.index'))
            ->post(route('produk.store'), $data)
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('satuan_pemakaian');
    }

    public function test_store_rejects_missing_material_usage_unit(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'satuan_pemakaian' => [null],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('satuan_pemakaian.0');
    }

    public function test_store_rejects_material_usage_unit_longer_than_30_characters(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'satuan_pemakaian' => [str_repeat('a', 31)],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('satuan_pemakaian.0');
    }

    public function test_store_does_not_create_product_when_composition_is_invalid(): void
    {
        $this->from(route('produk.index'))
            ->post(route('produk.store'), $this->dataProdukValid([
                'jumlah_pemakaian' => [0],
            ]))
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors('jumlah_pemakaian.0');

        $this->assertSame(0, Produk::query()->count());
        $this->assertSame(0, KomposisiProduk::query()->count());
    }

    public function test_customer_service_cannot_store_product(): void
    {
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->actingAs($customerService)
            ->post(route('produk.store'), $this->dataProdukValid())
            ->assertForbidden();

        $this->assertSame(0, Produk::query()->count());
        $this->assertSame(0, KomposisiProduk::query()->count());
    }

    public function test_store_returns_forbidden_when_user_has_no_branch(): void
    {
        $userTanpaCabang = $this->buatUser('Admin Pusat');

        $this->actingAs($userTanpaCabang)
            ->post(route('produk.store'), $this->dataProdukValid())
            ->assertForbidden();

        $this->assertSame(0, Produk::query()->count());
    }

    /* -----------------------------------------------------------------
     | UPDATE: UBAH PRODUK DAN KOMPOSISI
     |------------------------------------------------------------------ */

    public function test_update_changes_product_and_replaces_old_composition(): void
    {
        $produk = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Sebelum Edit',
        ]);
        $bahanLama = $this->buatBahanBaku(['nama_bahan' => 'Bahan Lama']);
        $bahanBaru = $this->buatBahanBaku(['nama_bahan' => 'Bahan Baru']);
        $komposisiLama = $this->buatKomposisi($produk, $bahanLama);

        $data = [
            'nama_produk' => 'Produk Setelah Edit',
            'satuan' => 'pcs',
            'upah_operator' => 3500,
            'bahan_baku_id' => [$bahanBaru->id],
            'jumlah_pemakaian' => [3.75],
            'satuan_pemakaian' => ['meter'],
        ];

        $response = $this->put(route('produk.update', $produk->id), $data);

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHas('success');

        $produk->refresh();
        $this->assertSame('Produk Setelah Edit', $produk->nama_produk);
        $this->assertSame('pcs', $produk->satuan);
        $this->assertEquals(3500, (float) $produk->upah_operator);
        $this->assertSame('aktif', $produk->status);

        $this->assertDatabaseMissing('komposisi_produks', [
            'id' => $komposisiLama->id,
        ]);
        $this->assertDatabaseHas('komposisi_produks', [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaru->id,
            'jumlah_pemakaian' => 3.75,
            'satuan_pemakaian' => 'meter',
        ]);
        $this->assertSame(1, $produk->komposisi()->count());
    }

    public function test_update_allows_product_to_keep_its_own_name(): void
    {
        $produk = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Nama Produk Tetap',
        ]);
        $bahan = $this->buatBahanBaku();

        $response = $this->put(route('produk.update', $produk->id), [
            'nama_produk' => 'Nama Produk Tetap',
            'satuan' => 'lembar',
            'upah_operator' => 1000,
            'bahan_baku_id' => [$bahan->id],
            'jumlah_pemakaian' => [1],
            'satuan_pemakaian' => ['lembar'],
        ]);

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHasNoErrors();
    }

    public function test_update_rejects_name_already_used_by_another_product_in_same_branch(): void
    {
        $produkPertama = $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Pertama',
        ]);
        $this->buatProduk($this->cabangUtama, [
            'nama_produk' => 'Produk Kedua',
        ]);
        $bahan = $this->buatBahanBaku();

        $response = $this->from(route('produk.index'))
            ->put(route('produk.update', $produkPertama->id), [
                'nama_produk' => 'Produk Kedua',
                'satuan' => 'lembar',
                'upah_operator' => 1000,
                'bahan_baku_id' => [$bahan->id],
                'jumlah_pemakaian' => [1],
                'satuan_pemakaian' => ['lembar'],
            ]);

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHasErrors('nama_produk');
        $this->assertSame('Produk Pertama', $produkPertama->fresh()->nama_produk);
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);

        $this->from(route('produk.index'))
            ->put(route('produk.update', $produk->id), [])
            ->assertRedirect(route('produk.index'))
            ->assertSessionHasErrors([
                'nama_produk',
                'satuan',
                'upah_operator',
                'bahan_baku_id',
                'jumlah_pemakaian',
                'satuan_pemakaian',
            ]);
    }

    public function test_update_returns_not_found_for_product_from_another_branch(): void
    {
        $cabangLain = $this->buatCabang();
        $produkLain = $this->buatProduk($cabangLain);

        $this->put(route('produk.update', $produkLain->id), $this->dataProdukValid())
            ->assertNotFound();
    }

    public function test_update_returns_not_found_for_nonexistent_product(): void
    {
        $this->put(route('produk.update', 999999), $this->dataProdukValid())
            ->assertNotFound();
    }

    public function test_update_returns_forbidden_when_user_has_no_branch(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $userTanpaCabang = $this->buatUser('Admin Pusat');

        $this->actingAs($userTanpaCabang)
            ->put(route('produk.update', $produk->id), $this->dataProdukValid())
            ->assertForbidden();
    }

    public function test_customer_service_cannot_update_product(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $namaSebelum = $produk->nama_produk;
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->actingAs($customerService)
            ->put(route('produk.update', $produk->id), $this->dataProdukValid())
            ->assertForbidden();

        $this->assertSame($namaSebelum, $produk->fresh()->nama_produk);
    }

    /* -----------------------------------------------------------------
     | DESTROY: HAPUS PRODUK
     |------------------------------------------------------------------ */

    public function test_destroy_deletes_product_without_transaction_details(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $bahan = $this->buatBahanBaku();
        $this->buatKomposisi($produk, $bahan);

        $response = $this->delete(route('produk.destroy', $produk->id));

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing((new Produk())->getTable(), ['id' => $produk->id]);
        $this->assertDatabaseMissing('komposisi_produks', ['produk_id' => $produk->id]);
    }

    public function test_destroy_is_rejected_when_product_has_transaction_details(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $this->buatDetailTransaksi($produk);

        $response = $this->from(route('produk.index'))
            ->delete(route('produk.destroy', $produk->id));

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas((new Produk())->getTable(), ['id' => $produk->id]);
    }

    public function test_destroy_returns_not_found_for_product_from_another_branch(): void
    {
        $cabangLain = $this->buatCabang();
        $produkLain = $this->buatProduk($cabangLain);

        $this->delete(route('produk.destroy', $produkLain->id))
            ->assertNotFound();

        $this->assertDatabaseHas((new Produk())->getTable(), ['id' => $produkLain->id]);
    }

    public function test_destroy_returns_not_found_for_nonexistent_product(): void
    {
        $this->delete(route('produk.destroy', 999999))
            ->assertNotFound();
    }

    public function test_destroy_returns_forbidden_when_user_has_no_branch(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $userTanpaCabang = $this->buatUser('Admin Pusat');

        $this->actingAs($userTanpaCabang)
            ->delete(route('produk.destroy', $produk->id))
            ->assertForbidden();

        $this->assertDatabaseHas((new Produk())->getTable(), ['id' => $produk->id]);
    }

    public function test_customer_service_cannot_delete_product(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->actingAs($customerService)
            ->delete(route('produk.destroy', $produk->id))
            ->assertForbidden();

        $this->assertDatabaseHas((new Produk())->getTable(), ['id' => $produk->id]);
    }

    /* -----------------------------------------------------------------
     | TOGGLE STATUS
     |------------------------------------------------------------------ */

    public function test_toggle_status_changes_active_product_to_nonactive(): void
    {
        $produk = $this->buatProduk($this->cabangUtama, ['status' => 'aktif']);

        $response = $this->from(route('produk.index'))
            ->patch(route('produk.toggle-status', $produk->id));

        $response->assertRedirect(route('produk.index'));
        $response->assertSessionHas('success');
        $this->assertSame('nonaktif', $produk->fresh()->status);
    }

    public function test_toggle_status_changes_nonactive_product_to_active(): void
    {
        $produk = $this->buatProduk($this->cabangUtama, ['status' => 'nonaktif']);

        $response = $this->from(route('produk.index', ['status' => 'nonaktif']))
            ->patch(route('produk.toggle-status', $produk->id));

        $response->assertRedirect(route('produk.index', ['status' => 'nonaktif']));
        $response->assertSessionHas('success');
        $this->assertSame('aktif', $produk->fresh()->status);
    }

    public function test_toggle_status_returns_not_found_for_product_from_another_branch(): void
    {
        $cabangLain = $this->buatCabang();
        $produkLain = $this->buatProduk($cabangLain);

        $this->patch(route('produk.toggle-status', $produkLain->id))
            ->assertNotFound();

        $this->assertSame('aktif', $produkLain->fresh()->status);
    }

    public function test_toggle_status_returns_not_found_for_nonexistent_product(): void
    {
        $this->patch(route('produk.toggle-status', 999999))
            ->assertNotFound();
    }

    public function test_toggle_status_returns_forbidden_when_user_has_no_branch(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $userTanpaCabang = $this->buatUser('Admin Pusat');

        $this->actingAs($userTanpaCabang)
            ->patch(route('produk.toggle-status', $produk->id))
            ->assertForbidden();

        $this->assertSame('aktif', $produk->fresh()->status);
    }

    public function test_customer_service_cannot_toggle_product_status(): void
    {
        $produk = $this->buatProduk($this->cabangUtama);
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $this->actingAs($customerService)
            ->patch(route('produk.toggle-status', $produk->id))
            ->assertForbidden();

        $this->assertSame('aktif', $produk->fresh()->status);
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function buatCabang(array $attributes = []): Cabang
    {
        $nomor = ++$this->nomorCabang;

        return Cabang::create(array_merge([
            'nama' => 'Cabang Produk Uji ' . $nomor,
            'alamat' => 'Alamat Cabang Produk Uji ' . $nomor,
            'no_hp' => '08123456' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $nomor = ++$this->nomorUser;
        $slugRole = strtolower(str_replace(' ', '-', $role));

        return User::factory()->create([
            'name' => 'User Produk Uji ' . $nomor,
            'username' => 'user-produk-' . $slugRole . '-' . $nomor,
            'email' => 'user-produk-' . $nomor . '@example.test',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatBahanBaku(array $attributes = []): BahanBaku
    {
        $nomor = ++$this->nomorBahan;

        return BahanBaku::create(array_merge([
            'cabang_id' => $this->cabangUtama->id,
            'kode_bahan' => 'BB-PRODUK-' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
            'nama_bahan' => 'Bahan Produk Uji ' . $nomor,
            'spesifikasi' => 'Spesifikasi bahan pengujian',
            'satuan' => 'pcs',
            'stok' => 100,
            'harga_rata_rata' => 2500,
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatProduk(Cabang $cabang, array $attributes = []): Produk
    {
        $nomor = ++$this->nomorProduk;

        return Produk::create(array_merge([
            'cabang_id' => $cabang->id,
            'user_id' => $this->adminPusat->id,
            'nama_produk' => 'Produk Uji ' . $nomor,
            'satuan' => 'lembar',
            'upah_operator' => 1500,
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatKomposisi(
        Produk $produk,
        BahanBaku $bahan,
        array $attributes = []
    ): KomposisiProduk {
        return $produk->komposisi()->create(array_merge([
            'bahan_baku_id' => $bahan->id,
            'jumlah_pemakaian' => 1.25,
            'satuan_pemakaian' => 'pcs',
        ], $attributes));
    }

    private function buatTransaksi(Cabang $cabang, ?User $user = null): Transaksi
    {
        $nomor = ++$this->nomorTransaksi;
        $user ??= $this->adminPusat;

        return Transaksi::create([
            'no_referensi' => 'TRX-PRODUK-UJI-' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'nama_pelanggan' => 'Pelanggan Uji Produk',
            'nomor_hp' => '081234567890',
            'cabang_id' => $cabang->id,
            'user_id' => $user->id,
            'total_transaksi' => 10000,
            'total_hpp' => 5000,
        ]);
    }

    private function buatDetailTransaksi(Produk $produk): DetailTransaksi
    {
        $transaksi = $this->buatTransaksi(
            Cabang::query()->findOrFail($produk->cabang_id)
        );

        return $produk->detailTransaksis()->create([
            'transaksi_id' => $transaksi->id,
            'jumlah' => 1,
            'lebar' => null,
            'tinggi' => null,
            'luas' => null,
            'harga_satuan' => 10000,
            'hpp_satuan' => 5000,
            'subtotal' => 10000,
            'jasa_edit' => 0,
            'total_hpp' => 5000,
        ]);
    }

    private function dataProdukValid(array $attributes = []): array
    {
        $bahan = $this->buatBahanBaku();

        return array_merge([
            'nama_produk' => 'Produk Baru Uji',
            'satuan' => 'lembar',
            'upah_operator' => 2000,
            'bahan_baku_id' => [$bahan->id],
            'jumlah_pemakaian' => [1.5],
            'satuan_pemakaian' => ['lembar'],
        ], $attributes);
    }
}
