<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private const TANGGAL_UJI = '2026-11-04';

    protected User $adminPusat;
    protected Cabang $cabangUtama;

    private int $nomorTransaksi = 0;
    private int $nomorJurnal = 0;
    private int $nomorAkun = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Tanggal dibuat tetap agar pengujian tidak bergantung pada tanggal komputer.
        Carbon::setTestNow(Carbon::parse(self::TANGGAL_UJI . ' 10:30:00'));

        $this->cabangUtama = $this->buatCabang('Cabang Utama');
        $this->adminPusat = $this->buatUser('Admin Pusat');

        $this->actingAs($this->adminPusat);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     | AKSES DAN TAMPILAN DASHBOARD
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_dashboard_and_sees_all_branches(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('isAdminPusat', true);
        $response->assertViewHas('isAdminCabang', false);
        $response->assertViewHas('judulDashboard', 'Dashboard Admin Pusat');
        $response->assertViewHas('daftarCabang', function ($daftarCabang) use ($cabangKedua): bool {
            return $daftarCabang->pluck('id')->contains($this->cabangUtama->id)
                && $daftarCabang->pluck('id')->contains($cabangKedua->id)
                && $daftarCabang->count() === 2;
        });
        $response->assertViewHas('totalCabang', 2);
        $response->assertViewHas('cabangId', null);
        $response->assertViewHas('ringkasanCabang', fn ($data) => $data->count() === 2);
    }

    public function test_admin_cabang_can_open_dashboard_and_only_sees_own_branch_in_filter(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('isAdminPusat', false);
        $response->assertViewHas('isAdminCabang', true);
        $response->assertViewHas('judulDashboard', 'Dashboard Admin Cabang');
        $response->assertViewHas('namaCabang', 'Cabang Utama');
        $response->assertViewHas('cabangId', $this->cabangUtama->id);
        $response->assertViewHas('daftarCabang', function ($daftarCabang) use ($cabangKedua): bool {
            return $daftarCabang->count() === 1
                && $daftarCabang->first()->id === $this->cabangUtama->id
                && !$daftarCabang->pluck('id')->contains($cabangKedua->id);
        });
        $response->assertViewHas('totalCabang', 1);
        $response->assertViewHas('ringkasanCabang', fn ($data) => $data->isEmpty());
    }

    public function test_admin_cabang_cannot_change_branch_by_query_parameter(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $this->buatTransaksi([
            'cabang_id' => $this->cabangUtama->id,
            'total_transaksi' => 300000,
            'total_hpp' => 100000,
        ]);
        $this->buatTransaksi([
            'cabang_id' => $cabangKedua->id,
            'total_transaksi' => 900000,
            'total_hpp' => 200000,
        ]);

        $response = $this->actingAs($adminCabang)->get(route('admin.dashboard', [
            'tanggal' => self::TANGGAL_UJI,
            'cabang_id' => $cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabangId', $this->cabangUtama->id);
        $response->assertViewHas('jumlahTransaksi', 1);
        $response->assertViewHas('omzet', 300000);
        $response->assertViewHas('totalHpp', 100000);
        $response->assertViewHas('laba', 200000);
    }

    public function test_customer_service_cannot_open_admin_dashboard(): void
    {
        // Route dashboard admin ditujukan untuk Admin Pusat dan Admin Cabang.
        // Test ini akan mengungkap jika route belum diberi middleware role.
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $response = $this->actingAs($customerService)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertHeader('Cache-Control');
        $response->assertHeader('Pragma', 'no-cache');

        $cacheControl = strtolower(
            $response->headers->get('Cache-Control', '')
        );

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    /* -----------------------------------------------------------------
     | FILTER TANGGAL DAN CABANG
     |------------------------------------------------------------------ */

    public function test_dashboard_uses_today_when_date_filter_is_not_provided(): void
    {
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 450000,
            'total_hpp' => 150000,
        ]);

        $this->buatTransaksi([
            'tanggal' => '2026-11-03',
            'total_transaksi' => 800000,
            'total_hpp' => 300000,
        ]);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('tanggal', self::TANGGAL_UJI);
        $response->assertViewHas('jumlahTransaksi', 1);
        $response->assertViewHas('omzet', 450000);
    }

    public function test_admin_pusat_can_filter_dashboard_by_selected_branch(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');

        $this->buatTransaksi([
            'cabang_id' => $this->cabangUtama->id,
            'total_transaksi' => 250000,
            'total_hpp' => 100000,
        ]);
        $this->buatTransaksi([
            'cabang_id' => $cabangKedua->id,
            'total_transaksi' => 750000,
            'total_hpp' => 300000,
        ]);

        $response = $this->get(route('admin.dashboard', [
            'tanggal' => self::TANGGAL_UJI,
            'cabang_id' => $cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabangId', $cabangKedua->id);
        $response->assertViewHas('jumlahTransaksi', 1);
        $response->assertViewHas('omzet', 750000);
        $response->assertViewHas('totalHpp', 300000);
        $response->assertViewHas('laba', 450000);
    }

    public function test_admin_pusat_sees_all_branches_when_branch_filter_is_semua(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');

        $this->buatTransaksi([
            'cabang_id' => $this->cabangUtama->id,
            'total_transaksi' => 200000,
            'total_hpp' => 50000,
        ]);
        $this->buatTransaksi([
            'cabang_id' => $cabangKedua->id,
            'total_transaksi' => 300000,
            'total_hpp' => 100000,
        ]);

        $response = $this->get(route('admin.dashboard', [
            'tanggal' => self::TANGGAL_UJI,
            'cabang_id' => 'semua',
        ]));

        $response->assertOk();
        $response->assertViewHas('cabangId', null);
        $response->assertViewHas('jumlahTransaksi', 2);
        $response->assertViewHas('omzet', 500000);
        $response->assertViewHas('totalHpp', 150000);
        $response->assertViewHas('laba', 350000);
    }

    /* -----------------------------------------------------------------
     | STATISTIK TRANSAKSI DAN PERSENTASE
     |------------------------------------------------------------------ */

    public function test_dashboard_calculates_today_statistics_and_comparison_with_previous_day(): void
    {
        // Hari ini: omzet 1.500.000, HPP 400.000, laba 1.100.000, 2 transaksi.
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 1000000,
            'total_hpp' => 300000,
        ]);
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 500000,
            'total_hpp' => 100000,
        ]);

        // Hari sebelumnya: omzet 1.000.000, HPP 500.000, laba 500.000, 1 transaksi.
        $this->buatTransaksi([
            'tanggal' => '2026-11-03',
            'total_transaksi' => 1000000,
            'total_hpp' => 500000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('jumlahTransaksi', 2);
        $response->assertViewHas('omzet', 1500000);
        $response->assertViewHas('totalHpp', 400000);
        $response->assertViewHas('laba', 1100000);
        $response->assertViewHas('persenOmzet', 50.0);
        $response->assertViewHas('persenTransaksi', 100.0);
        $response->assertViewHas('persenHpp', -20.0);
        $response->assertViewHas('persenLaba', 120.0);
    }

    public function test_dashboard_uses_thirty_three_percent_estimate_when_hpp_is_zero(): void
    {
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 1000000,
            'total_hpp' => 0,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('totalHpp', 0);
        $response->assertViewHas('laba', 330000.0);
    }

    public function test_percentage_is_one_hundred_when_previous_value_is_zero_and_current_value_is_positive(): void
    {
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 250000,
            'total_hpp' => 50000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('persenOmzet', 100.0);
        $response->assertViewHas('persenTransaksi', 100.0);
    }

    public function test_percentage_is_zero_when_current_and_previous_values_are_zero(): void
    {
        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('persenOmzet', 0);
        $response->assertViewHas('persenTransaksi', 0);
        $response->assertViewHas('persenHpp', 0);
        $response->assertViewHas('persenLaba', 0);
    }

    /* -----------------------------------------------------------------
     | TRANSAKSI TERBARU DAN PRODUK TERLARIS
     |------------------------------------------------------------------ */

    public function test_dashboard_displays_only_five_latest_transactions_for_selected_date(): void
    {
        $transactions = [];

        for ($i = 1; $i <= 6; $i++) {
            $transactions[] = $this->buatTransaksi([
                'tanggal' => self::TANGGAL_UJI,
                'nama_pelanggan' => 'Pelanggan ' . $i,
                'total_transaksi' => $i * 100000,
                'total_hpp' => $i * 30000,
            ]);
        }

        // Transaksi tanggal lain tidak boleh masuk ke daftar transaksi hari terpilih.
        $this->buatTransaksi([
            'tanggal' => '2026-11-03',
            'nama_pelanggan' => 'Pelanggan Kemarin',
            'total_transaksi' => 900000,
            'total_hpp' => 300000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('transaksiTerbaru', function ($data) use ($transactions): bool {
            return $data->count() === 5
                && $data->first()->id === $transactions[5]->id
                && !$data->pluck('id')->contains($transactions[0]->id);
        });
    }

    public function test_dashboard_groups_best_selling_products_by_quantity(): void
    {
        $produkA = $this->buatProduk('Produk A');
        $produkB = $this->buatProduk('Produk B');

        $transaksi = $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 1000000,
            'total_hpp' => 400000,
        ]);

        $this->buatDetailTransaksi($transaksi, $produkA, [
            'jumlah' => 5,
            'subtotal' => 250000,
        ]);
        $this->buatDetailTransaksi($transaksi, $produkA, [
            'jumlah' => 3,
            'subtotal' => 150000,
        ]);
        $this->buatDetailTransaksi($transaksi, $produkB, [
            'jumlah' => 6,
            'subtotal' => 600000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('produkTerlaris', function ($data) use ($produkA, $produkB): bool {
            return $data->count() === 2
                && $data->first()->produk_id === $produkA->id
                && (float) $data->first()->total_terjual === 8.0
                && (float) $data->first()->total_penjualan === 400000.0
                && $data->last()->produk_id === $produkB->id
                && (float) $data->last()->total_terjual === 6.0;
        });
    }

    public function test_dashboard_displays_no_best_selling_products_when_there_are_no_details(): void
    {
        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('produkTerlaris', fn ($data) => $data->isEmpty());
        $response->assertSee('Belum ada data produk terlaris.');
    }

    /* -----------------------------------------------------------------
     | JURNAL DAN RINGKASAN DEBIT KREDIT
     |------------------------------------------------------------------ */

    public function test_dashboard_displays_five_latest_journals(): void
    {
        $jurnals = [];

        for ($i = 1; $i <= 6; $i++) {
            $jurnals[] = $this->buatJurnal([
                'tanggal' => Carbon::parse(self::TANGGAL_UJI)->subDays($i - 1)->toDateString(),
                'keterangan' => 'Jurnal ' . $i,
            ]);
        }

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('jurnalTerbaru', function ($data) use ($jurnals): bool {
            return $data->count() === 5
                && $data->first()->id === $jurnals[0]->id
                && !$data->pluck('id')->contains($jurnals[5]->id);
        });
    }

    public function test_dashboard_sums_debit_and_credit_for_selected_date(): void
    {
        $akun = $this->buatAkun();

        $jurnalHariIni = $this->buatJurnal(['tanggal' => self::TANGGAL_UJI]);
        $detailHariIni = DetailJurnal::create([
            'jurnal_id' => $jurnalHariIni->id,
            'akun_id' => $akun->id,
            'debit' => 750000,
            'kredit' => 250000,
        ]);

        $jurnalCabangLain = $this->buatJurnal([
            'cabang_id' => $this->buatCabang('Cabang Lain')->id,
            'tanggal' => self::TANGGAL_UJI,
        ]);
        DetailJurnal::create([
            'jurnal_id' => $jurnalCabangLain->id,
            'akun_id' => $akun->id,
            'debit' => 100000,
            'kredit' => 100000,
        ]);

        $jurnalKemarin = $this->buatJurnal(['tanggal' => '2026-11-03']);
        DetailJurnal::create([
            'jurnal_id' => $jurnalKemarin->id,
            'akun_id' => $akun->id,
            'debit' => 500000,
            'kredit' => 500000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('totalDebit', 850000.0);
        $response->assertViewHas('totalKredit', 350000.0);
    }

    public function test_admin_cabang_only_sees_journal_totals_from_own_branch(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $akun = $this->buatAkun();

        $jurnalSendiri = $this->buatJurnal(['tanggal' => self::TANGGAL_UJI]);
        DetailJurnal::create([
            'jurnal_id' => $jurnalSendiri->id,
            'akun_id' => $akun->id,
            'debit' => 400000,
            'kredit' => 400000,
        ]);

        $jurnalCabangLain = $this->buatJurnal([
            'cabang_id' => $cabangKedua->id,
            'tanggal' => self::TANGGAL_UJI,
        ]);
        DetailJurnal::create([
            'jurnal_id' => $jurnalCabangLain->id,
            'akun_id' => $akun->id,
            'debit' => 900000,
            'kredit' => 900000,
        ]);

        $response = $this->actingAs($adminCabang)->get(route('admin.dashboard', [
            'tanggal' => self::TANGGAL_UJI,
        ]));

        $response->assertOk();
        $response->assertViewHas('totalDebit', 400000.0);
        $response->assertViewHas('totalKredit', 400000.0);
        $response->assertViewHas('jurnalTerbaru', fn ($data) => $data->count() === 1);
    }

    /* -----------------------------------------------------------------
     | GRAFIK
     |------------------------------------------------------------------ */

    public function test_dashboard_builds_seven_day_omzet_and_transaction_chart(): void
    {
        $tanggalAwal = Carbon::parse(self::TANGGAL_UJI)->subDays(6);

        for ($i = 0; $i < 7; $i++) {
            $this->buatTransaksi([
                'tanggal' => $tanggalAwal->copy()->addDays($i)->toDateString(),
                'total_transaksi' => ($i + 1) * 100000,
                'total_hpp' => 0,
            ]);
        }

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('tanggalChart', fn ($data) => count($data) === 7);
        $response->assertViewHas('omzetChart', function ($data): bool {
            return count($data) === 7
                && (float) $data[0] === 100000.0
                && (float) $data[6] === 700000.0;
        });
        $response->assertViewHas('transaksiChart', function ($data): bool {
            return count($data) === 7
                && (int) $data[0] === 1
                && (int) $data[6] === 1;
        });
    }

    public function test_dashboard_builds_hourly_chart_from_eight_until_sixteen(): void
    {
        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 1500000,
            'total_hpp' => 500000,
            'created_at' => Carbon::parse(self::TANGGAL_UJI . ' 08:15:00'),
        ]);

        $this->buatTransaksi([
            'tanggal' => self::TANGGAL_UJI,
            'total_transaksi' => 900000,
            'total_hpp' => 300000,
            'created_at' => Carbon::parse(self::TANGGAL_UJI . ' 09:30:00'),
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('chartLabels', function ($labels): bool {
            return $labels === [
                '08:00', '09:00', '10:00', '11:00', '12:00',
                '13:00', '14:00', '15:00', '16:00',
            ];
        });
        $response->assertViewHas('chartData', function ($data): bool {
            return count($data) === 9
                && (float) $data[0] === 1.5
                && (float) $data[1] === 0.9;
        });
    }

    /* -----------------------------------------------------------------
     | WIDGET TOTAL MASTER DATA DAN RINGKASAN CABANG
     |------------------------------------------------------------------ */

    public function test_admin_pusat_sees_all_master_data_totals_and_branch_summary(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');
        $this->buatUser('Admin Cabang', $this->cabangUtama);
        $this->buatUser('Customer Service', $cabangKedua);
        $this->buatProduk('Produk A');
        $this->buatAkun();

        $this->buatTransaksi([
            'cabang_id' => $this->cabangUtama->id,
            'total_transaksi' => 1000000,
            'total_hpp' => 400000,
        ]);
        $this->buatTransaksi([
            'cabang_id' => $cabangKedua->id,
            'total_transaksi' => 500000,
            'total_hpp' => 200000,
        ]);

        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertViewHas('totalCabang', 2);
        $response->assertViewHas('totalUser', 3);
        $response->assertViewHas('totalProduk', 1);
        $response->assertViewHas('totalAkun', 1);
        $response->assertViewHas('ringkasanCabang', function ($data) use ($cabangKedua): bool {
            $cabang = $data->firstWhere('id', $cabangKedua->id);

            return $data->count() === 2
                && $cabang !== null
                && (float) $cabang->omzet === 500000.0
                && (float) $cabang->hpp === 200000.0
                && (float) $cabang->laba === 300000.0;
        });
    }

    public function test_admin_cabang_sees_only_own_user_count_but_global_product_and_account_totals(): void
    {
        $cabangKedua = $this->buatCabang('Cabang Kedua');
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $this->buatUser('Customer Service', $this->cabangUtama);
        $this->buatUser('Admin Cabang', $cabangKedua);
        $this->buatProduk('Produk Cabang Utama');
        $this->buatProduk('Produk Cabang Kedua');
        $this->buatAkun();
        $this->buatAkun();

        $response = $this->actingAs($adminCabang)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalCabang', 1);
        $response->assertViewHas('totalUser', 2);
        $response->assertViewHas('totalProduk', 2);
        $response->assertViewHas('totalAkun', 2);
    }

    public function test_dashboard_renders_when_there_are_no_transactions_journals_or_products(): void
    {
        $response = $this->get(route('admin.dashboard', ['tanggal' => self::TANGGAL_UJI]));

        $response->assertOk();
        $response->assertSee('Belum ada transaksi pada tanggal ini.');
        $response->assertSee('Belum ada mutasi jurnal.');
        $response->assertSee('Belum ada data produk terlaris.');
        $response->assertViewHas('jumlahTransaksi', 0);
        $response->assertViewHas('omzet', 0);
        $response->assertViewHas('totalHpp', 0);
        $response->assertViewHas('laba', 0);
    }

    /* -----------------------------------------------------------------
     | DATA FACTORY MANUAL UNTUK TEST
     |------------------------------------------------------------------ */

    private function buatCabang(string $nama): Cabang
    {
        return Cabang::create([
            'nama' => $nama,
            'alamat' => 'Alamat ' . $nama,
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        return User::factory()->create([
            'name' => $role . ' Pengujian',
            'email' => strtolower(str_replace(' ', '-', $role)) . '-' . uniqid() . '@example.test',
            'username' => strtolower(str_replace(' ', '-', $role)) . '-' . uniqid(),
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatTransaksi(array $attributes = []): Transaksi
    {
        $createdAt = $attributes['created_at'] ?? null;
        unset($attributes['created_at']);

        $transaksi = Transaksi::create(array_merge([
            'no_referensi' => 'TRX-TEST-' . (++$this->nomorTransaksi),
            'tanggal' => self::TANGGAL_UJI,
            'nama_pelanggan' => 'Pelanggan Pengujian',
            'nomor_hp' => '081234567890',
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->adminPusat->id,
            'total_transaksi' => 0,
            'total_hpp' => 0,
        ], $attributes));

        if ($createdAt !== null) {
            $transaksi->forceFill(['created_at' => $createdAt])->save();
        }

        return $transaksi;
    }

    private function buatProduk(string $nama): Produk
    {
        return Produk::create([
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->adminPusat->id,
            'nama_produk' => $nama,
            'satuan' => 'lembar',
            'upah_operator' => 0,
            'status' => 'aktif',
        ]);
    }

    private function buatDetailTransaksi(
        Transaksi $transaksi,
        Produk $produk,
        array $attributes = []
    ): DetailTransaksi {
        return DetailTransaksi::create(array_merge([
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'jumlah' => 1,
            'lebar' => null,
            'tinggi' => null,
            'luas' => null,
            'harga_satuan' => 100000,
            'hpp_satuan' => 40000,
            'subtotal' => 100000,
            'jasa_edit' => 0,
            'total_hpp' => 40000,
        ], $attributes));
    }

    private function buatJurnal(array $attributes = []): Jurnal
    {
        return Jurnal::create(array_merge([
            'no_referensi' => 'JRN-TEST-' . (++$this->nomorJurnal),
            'tanggal' => self::TANGGAL_UJI,
            'keterangan' => 'Jurnal pengujian',
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->adminPusat->id,
        ], $attributes));
    }

    private function buatAkun(): Akun
    {
        $nomor = ++$this->nomorAkun;

        return Akun::create([
            'kode_akun' => '1-' . (1100 + ($nomor * 100)),
            'nama_akun' => 'Akun Pengujian ' . $nomor,
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'saldo_normal' => 'Debit',
            'status' => 'aktif',
        ]);
    }
}
