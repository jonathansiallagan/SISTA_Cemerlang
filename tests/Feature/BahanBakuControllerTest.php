<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\DetailPembelianBahan;
use App\Models\PembelianBahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BahanBakuControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorBahan = 0;

    protected User $adminPusat;

    protected function setUp(): void
    {
        parent::setUp();

        // Semua route Bahan Baku berada dalam middleware auth dan
        // role:Admin Pusat,Admin Cabang.
        $this->adminPusat = User::factory()->create([
            'name' => 'Admin Pengujian',
            'email' => 'admin-bahan@example.test',
            'username' => 'admin-bahan',
            'role' => 'Admin Pusat',
        ]);

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
     | AKSES DAN INDEX
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('bahan-baku.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_bahan_baku_page(): void
    {
        $response = $this->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertViewIs('admin.bahan_baku.index');
        $response->assertViewHas('bahanBakus');
        $response->assertViewHas('kodeBahan', 'BB001');
    }

    public function test_admin_cabang_can_open_bahan_baku_page(): void
    {
        $adminCabang = User::factory()->create([
            'role' => 'Admin Cabang',
            'email' => 'admin-cabang-bahan@example.test',
            'username' => 'admin-cabang-bahan',
        ]);

        $response = $this->actingAs($adminCabang)
            ->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertViewIs('admin.bahan_baku.index');
    }

    public function test_customer_service_cannot_access_bahan_baku_page(): void
    {
        $customerService = User::factory()->create([
            'role' => 'Customer Service',
            'email' => 'cs-bahan@example.test',
            'username' => 'cs-bahan',
        ]);

        $response = $this->actingAs($customerService)
            ->get(route('bahan-baku.index'));

        $response->assertForbidden();
    }

    public function test_index_displays_bahan_baku_sorted_by_name(): void
    {
        $this->buatBahanBaku(['nama_bahan' => 'Tinta Printer']);
        $this->buatBahanBaku(['nama_bahan' => 'Kertas A4']);
        $this->buatBahanBaku(['nama_bahan' => 'Flexi Banner']);

        $response = $this->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertViewHas('bahanBakus', function ($bahanBakus): bool {
            return $bahanBakus->pluck('nama_bahan')->all() === [
                'Flexi Banner',
                'Kertas A4',
                'Tinta Printer',
            ];
        });
    }

    public function test_index_generates_next_code_based_on_highest_existing_code(): void
    {
        $this->buatBahanBaku(['kode_bahan' => 'BB001']);
        $this->buatBahanBaku(['kode_bahan' => 'BB004']);
        $this->buatBahanBaku(['kode_bahan' => 'BB002']);

        $response = $this->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertViewHas('kodeBahan', 'BB005');
    }

    public function test_index_ignores_existing_codes_that_do_not_match_bb_number_format(): void
    {
        $this->buatBahanBaku(['kode_bahan' => 'BBXYZ']);
        $this->buatBahanBaku(['kode_bahan' => 'BAHAN-10']);
        $this->buatBahanBaku(['kode_bahan' => 'BB007']);

        $response = $this->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertViewHas('kodeBahan', 'BB008');
    }

    public function test_index_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('bahan-baku.index'));

        $response->assertOk();
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        // Urutan directive Cache-Control dapat dinormalisasi oleh Symfony.
        $cacheControl = strtolower($response->headers->get('Cache-Control', ''));

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    /* -----------------------------------------------------------------
     | STORE
     |------------------------------------------------------------------ */

    public function test_store_creates_bahan_baku_with_generated_code_and_initial_stock(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => '  80 gsm, 500 lembar  ',
            'satuan' => 'rim',
            'status' => 'aktif',
            // Field ini sengaja dikirim untuk memastikan kode tetap dibuat server.
            'kode_bahan' => 'KODE-MANUAL',
            'stok' => 999,
            'harga_rata_rata' => 123456,
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHas('success', 'Data bahan baku berhasil ditambahkan.');

        $this->assertDatabaseHas('bahan_baku', [
            'kode_bahan' => 'BB001',
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => '80 gsm, 500 lembar',
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $bahan = BahanBaku::where('kode_bahan', 'BB001')->firstOrFail();
        $this->assertSame('0.000', $bahan->stok);
        $this->assertSame('0.00', $bahan->harga_rata_rata);
        $this->assertDatabaseMissing('bahan_baku', [
            'kode_bahan' => 'KODE-MANUAL',
        ]);
    }

    public function test_store_generates_next_code_after_existing_bahan(): void
    {
        $this->buatBahanBaku(['kode_bahan' => 'BB001']);
        $this->buatBahanBaku(['kode_bahan' => 'BB003']);

        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Tinta Printer',
            'spesifikasi' => 'Tinta CMYK',
            'satuan' => 'liter',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $this->assertDatabaseHas('bahan_baku', [
            'kode_bahan' => 'BB004',
            'nama_bahan' => 'Tinta Printer',
        ]);
    }

    public function test_store_trims_name_and_sets_blank_specification_to_null(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => '  Karton  ',
            'spesifikasi' => '   ',
            'satuan' => 'lembar',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $this->assertDatabaseHas('bahan_baku', [
            'nama_bahan' => 'Karton',
            'spesifikasi' => null,
            'satuan' => 'lembar',
            'status' => 'nonaktif',
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->from(route('bahan-baku.index'))
            ->post(route('bahan-baku.store'), []);

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHasErrors(['nama_bahan', 'satuan', 'status']);
        $this->assertDatabaseCount('bahan_baku', 0);
    }

    public function test_store_rejects_name_longer_than_150_characters(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => str_repeat('A', 151),
            'spesifikasi' => null,
            'satuan' => 'pcs',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama_bahan');
        $this->assertDatabaseCount('bahan_baku', 0);
    }

    public function test_store_rejects_specification_longer_than_150_characters(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => str_repeat('A', 151),
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('spesifikasi');
        $this->assertDatabaseCount('bahan_baku', 0);
    }

    public function test_store_rejects_duplicate_name(): void
    {
        $this->buatBahanBaku(['nama_bahan' => 'Kertas A4']);

        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => 'Spesifikasi baru',
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama_bahan');
        $this->assertDatabaseCount('bahan_baku', 1);
    }

    public function test_store_rejects_name_that_becomes_duplicate_after_trimming(): void
    {
        $this->buatBahanBaku(['nama_bahan' => 'Kertas A4']);

        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => '  Kertas A4  ',
            'spesifikasi' => null,
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama_bahan');
        $this->assertDatabaseCount('bahan_baku', 1);
    }

    public function test_store_rejects_invalid_unit(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => null,
            'satuan' => 'dus',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('satuan');
        $this->assertDatabaseCount('bahan_baku', 0);
    }

    public function test_store_rejects_invalid_status(): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => null,
            'satuan' => 'rim',
            'status' => 'tersedia',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseCount('bahan_baku', 0);
    }

    public static function satuanValidProvider(): array
    {
        return [
            'lembar' => ['lembar'],
            'rim' => ['rim'],
            'pcs' => ['pcs'],
            'roll' => ['roll'],
            'meter' => ['meter'],
            'm2' => ['m2'],
            'kg' => ['kg'],
            'liter' => ['liter'],
            'botol' => ['botol'],
            'pak' => ['pak'],
        ];
    }

    #[DataProvider('satuanValidProvider')]
    public function test_store_accepts_each_supported_unit(string $satuan): void
    {
        $response = $this->post(route('bahan-baku.store'), [
            'nama_bahan' => 'Bahan ' . $satuan,
            'spesifikasi' => null,
            'satuan' => $satuan,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $this->assertDatabaseHas('bahan_baku', [
            'nama_bahan' => 'Bahan ' . $satuan,
            'satuan' => $satuan,
        ]);
    }

    /* -----------------------------------------------------------------
     | UPDATE
     |------------------------------------------------------------------ */

    public function test_update_changes_bahan_baku_data(): void
    {
        $bahan = $this->buatBahanBaku([
            'kode_bahan' => 'BB010',
            'nama_bahan' => 'Kertas Lama',
            'spesifikasi' => 'Spesifikasi lama',
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response = $this->put(route('bahan-baku.update', $bahan->id), [
            'kode_bahan' => 'BB011',
            'nama_bahan' => 'Kertas Baru',
            'spesifikasi' => '  100 gsm  ',
            'satuan' => 'lembar',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHas('success', 'Data bahan baku berhasil diperbarui.');

        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'kode_bahan' => 'BB011',
            'nama_bahan' => 'Kertas Baru',
            'spesifikasi' => '100 gsm',
            'satuan' => 'lembar',
            'status' => 'nonaktif',
        ]);
    }

    public function test_update_allows_existing_code_and_name_of_the_same_record(): void
    {
        $bahan = $this->buatBahanBaku([
            'kode_bahan' => 'BB015',
            'nama_bahan' => 'Kertas A4',
        ]);

        $response = $this->put(route('bahan-baku.update', $bahan->id), [
            'kode_bahan' => 'BB015',
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => 'Spesifikasi diperbarui',
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'kode_bahan' => 'BB015',
            'nama_bahan' => 'Kertas A4',
        ]);
    }

    public function test_update_does_not_change_stock_or_average_price_from_form_input(): void
    {
        $bahan = $this->buatBahanBaku([
            'stok' => 25.500,
            'harga_rata_rata' => 15000,
        ]);

        $response = $this->put(route('bahan-baku.update', $bahan->id), [
            'kode_bahan' => $bahan->kode_bahan,
            'nama_bahan' => 'Nama Bahan Diperbarui',
            'spesifikasi' => 'Spesifikasi baru',
            'satuan' => 'pcs',
            'status' => 'aktif',
            // Field berikut tidak boleh diubah melalui form master bahan baku.
            'stok' => 999,
            'harga_rata_rata' => 1,
        ]);

        $response->assertRedirect(route('bahan-baku.index'));

        $bahan->refresh();
        $this->assertSame('25.500', $bahan->stok);
        $this->assertSame('15000.00', $bahan->harga_rata_rata);
    }

    public function test_update_rejects_duplicate_code(): void
    {
        $this->buatBahanBaku(['kode_bahan' => 'BB020', 'nama_bahan' => 'Bahan Satu']);
        $bahanKedua = $this->buatBahanBaku(['kode_bahan' => 'BB021', 'nama_bahan' => 'Bahan Dua']);

        $response = $this->put(route('bahan-baku.update', $bahanKedua->id), [
            'kode_bahan' => 'BB020',
            'nama_bahan' => 'Bahan Dua',
            'spesifikasi' => null,
            'satuan' => 'pcs',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('kode_bahan');
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahanKedua->id,
            'kode_bahan' => 'BB021',
        ]);
    }

    public function test_update_rejects_duplicate_name(): void
    {
        $this->buatBahanBaku(['nama_bahan' => 'Kertas A4']);
        $bahanKedua = $this->buatBahanBaku(['nama_bahan' => 'Kertas F4']);

        $response = $this->put(route('bahan-baku.update', $bahanKedua->id), [
            'kode_bahan' => $bahanKedua->kode_bahan,
            'nama_bahan' => 'Kertas A4',
            'spesifikasi' => null,
            'satuan' => 'rim',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama_bahan');
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->put(route('bahan-baku.update', $bahan->id), []);

        $response->assertSessionHasErrors([
            'kode_bahan',
            'nama_bahan',
            'satuan',
            'status',
        ]);
    }

    public function test_update_rejects_invalid_unit_and_status(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->put(route('bahan-baku.update', $bahan->id), [
            'kode_bahan' => $bahan->kode_bahan,
            'nama_bahan' => 'Nama Baru',
            'spesifikasi' => null,
            'satuan' => 'dus',
            'status' => 'rusak',
        ]);

        $response->assertSessionHasErrors(['satuan', 'status']);
    }

    public function test_update_rejects_name_and_specification_longer_than_150_characters(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->put(route('bahan-baku.update', $bahan->id), [
            'kode_bahan' => $bahan->kode_bahan,
            'nama_bahan' => str_repeat('N', 151),
            'spesifikasi' => str_repeat('S', 151),
            'satuan' => 'pcs',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['nama_bahan', 'spesifikasi']);
    }

    public function test_update_returns_404_when_bahan_does_not_exist(): void
    {
        $response = $this->put(route('bahan-baku.update', 999999), [
            'kode_bahan' => 'BB999',
            'nama_bahan' => 'Bahan Tidak Ada',
            'spesifikasi' => null,
            'satuan' => 'pcs',
            'status' => 'aktif',
        ]);

        $response->assertNotFound();
    }

    /* -----------------------------------------------------------------
     | DESTROY
     |------------------------------------------------------------------ */

    public function test_destroy_deletes_bahan_baku_without_purchase_details(): void
    {
        $bahan = $this->buatBahanBaku();

        $response = $this->delete(route('bahan-baku.destroy', $bahan->id));

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHas('success', 'Data bahan baku berhasil dihapus.');
        $this->assertDatabaseMissing('bahan_baku', [
            'id' => $bahan->id,
        ]);
    }

    public function test_destroy_is_rejected_when_bahan_is_used_in_purchase_detail(): void
    {
        $bahan = $this->buatBahanBaku();

        $pembelian = PembelianBahan::create([
            'no_pembelian' => 'PB-TEST-001',
            'tanggal_pembelian' => '2026-11-04',
            'nama_pemasok' => 'Pemasok Pengujian',
            'keterangan' => 'Pembelian untuk pengujian relasi',
            'total_harga' => 100000,
        ]);

        DetailPembelianBahan::create([
            'pembelian_bahan_id' => $pembelian->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 10,
            'harga_satuan' => 10000,
            'total_harga' => 100000,
        ]);

        $response = $this->delete(route('bahan-baku.destroy', $bahan->id));

        $response->assertRedirect(route('bahan-baku.index'));
        $response->assertSessionHas('error', 'Bahan baku tidak dapat dihapus karena masih digunakan pada transaksi terkait.');
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
        ]);
    }

    public function test_destroy_returns_404_when_bahan_does_not_exist(): void
    {
        $response = $this->delete(route('bahan-baku.destroy', 999999));

        $response->assertNotFound();
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function buatBahanBaku(array $attributes = []): BahanBaku
    {
        $this->nomorBahan++;

        return BahanBaku::create(array_merge([
            'kode_bahan' => 'BB' . str_pad((string) $this->nomorBahan, 3, '0', STR_PAD_LEFT),
            'nama_bahan' => 'Bahan Uji ' . $this->nomorBahan,
            'spesifikasi' => 'Spesifikasi bahan uji',
            'satuan' => 'pcs',
            'stok' => 12.500,
            'harga_rata_rata' => 2500,
            'status' => 'aktif',
        ], $attributes));
    }
}
