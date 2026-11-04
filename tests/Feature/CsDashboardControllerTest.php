<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CsDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Cabang $cabangUtama;
    protected User $customerService;

    private int $nomorTransaksi = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Menghindari kebutuhan manifest Vite ketika Blade dirender dalam test.
        $this->withoutVite();

        $this->cabangUtama = $this->buatCabang('Cabang Utama');
        $this->customerService = $this->buatUser(
            'Customer Service',
            $this->cabangUtama,
            'CS Utama'
        );

        $this->actingAs($this->customerService);
    }

    /* -----------------------------------------------------------------
     | AKSES DAN TAMPILAN DASHBOARD
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('cs.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_service_can_open_dashboard(): void
    {
        $this->buatTransaksi([
            'nama_pelanggan' => 'Pelanggan Dashboard',
            'total_transaksi' => 250000,
            'tanggal' => date('Y-m-d'),
        ]);

        $response = $this->get(route('cs.dashboard', [
            'tanggal' => date('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertViewIs('cs.dashboard');
        $response->assertViewHas('user', $this->customerService);
        $response->assertViewHas('namaCabang', 'Cabang Utama');
        $response->assertSee('Transaksi Terbaru');
        $response->assertSee('Pelanggan Dashboard');
    }

    public function test_admin_pusat_cannot_open_customer_service_dashboard(): void
    {
        $adminPusat = $this->buatUser('Admin Pusat', null, 'Admin Pusat');

        $response = $this->actingAs($adminPusat)->get(route('cs.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_cabang_cannot_open_customer_service_dashboard(): void
    {
        $adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangUtama,
            'Admin Cabang'
        );

        $response = $this->actingAs($adminCabang)->get(route('cs.dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('cs.dashboard'));

        $response->assertOk();
        $response->assertHeader('Cache-Control');
        $response->assertHeader('Pragma', 'no-cache');

        $cacheControl = strtolower($response->headers->get('Cache-Control', ''));

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    /* -----------------------------------------------------------------
     | TANGGAL DAN BATASAN DATA CABANG
     |------------------------------------------------------------------ */

    public function test_dashboard_uses_today_when_date_filter_is_not_provided(): void
    {
        $today = date('Y-m-d');
        $yesterday = Carbon::yesterday()->toDateString();

        $this->buatTransaksi([
            'tanggal' => $today,
            'total_transaksi' => 300000,
        ]);

        $this->buatTransaksi([
            'tanggal' => $yesterday,
            'total_transaksi' => 900000,
        ]);

        $response = $this->get(route('cs.dashboard'));

        $response->assertOk();
        $response->assertViewHas('tanggal', $today);
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 1
                && (float) $statistik['omzet_saya'] === 300000.0
                && $statistik['transaksi_cabang'] === 1
                && (float) $statistik['omzet_cabang'] === 300000.0;
        });
    }

    public function test_dashboard_filters_transactions_by_selected_date(): void
    {
        $tanggalDipilih = '2026-10-20';

        $this->buatTransaksi([
            'tanggal' => $tanggalDipilih,
            'total_transaksi' => 150000,
        ]);

        $this->buatTransaksi([
            'tanggal' => '2026-10-21',
            'total_transaksi' => 700000,
        ]);

        $response = $this->get(route('cs.dashboard', [
            'tanggal' => $tanggalDipilih,
        ]));

        $response->assertOk();
        $response->assertViewHas('tanggal', $tanggalDipilih);
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 1
                && (float) $statistik['omzet_saya'] === 150000.0
                && $statistik['transaksi_cabang'] === 1
                && (float) $statistik['omzet_cabang'] === 150000.0;
        });
    }

    public function test_dashboard_only_counts_transactions_from_own_branch(): void
    {
        $cabangLain = $this->buatCabang('Cabang Lain');
        $userCabangLain = $this->buatUser(
            'Customer Service',
            $cabangLain,
            'CS Cabang Lain'
        );
        $tanggal = '2026-10-22';

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 200000,
        ]);

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'cabang_id' => $cabangLain->id,
            'user_id' => $userCabangLain->id,
            'total_transaksi' => 800000,
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 1
                && (float) $statistik['omzet_saya'] === 200000.0
                && $statistik['transaksi_cabang'] === 1
                && (float) $statistik['omzet_cabang'] === 200000.0;
        });
        $response->assertViewHas('transaksiTerbaru', fn ($data) => $data->count() === 1);
    }

    /* -----------------------------------------------------------------
     | STATISTIK TRANSAKSI SAYA DAN TRANSAKSI CABANG
     |------------------------------------------------------------------ */

    public function test_personal_statistics_only_include_transactions_created_by_logged_in_user(): void
    {
        $csLain = $this->buatUser(
            'Customer Service',
            $this->cabangUtama,
            'CS Lain'
        );
        $tanggal = '2026-10-23';

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 100000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 250000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'user_id' => $csLain->id,
            'total_transaksi' => 500000,
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 2
                && (float) $statistik['omzet_saya'] === 350000.0;
        });
    }

    public function test_branch_statistics_include_transactions_from_all_cs_in_same_branch(): void
    {
        $csLain = $this->buatUser(
            'Customer Service',
            $this->cabangUtama,
            'CS Rekan'
        );
        $tanggal = '2026-10-24';

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 100000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'user_id' => $csLain->id,
            'total_transaksi' => 400000,
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_cabang'] === 2
                && (float) $statistik['omzet_cabang'] === 500000.0;
        });
    }

    public function test_dashboard_personal_and_branch_statistics_are_calculated_separately(): void
    {
        $csLain = $this->buatUser(
            'Customer Service',
            $this->cabangUtama,
            'CS Kedua'
        );
        $cabangLain = $this->buatCabang('Cabang Pembanding');
        $userCabangLain = $this->buatUser(
            'Customer Service',
            $cabangLain,
            'CS Cabang Pembanding'
        );
        $tanggal = '2026-10-25';

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 100000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'total_transaksi' => 200000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'user_id' => $csLain->id,
            'total_transaksi' => 300000,
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'cabang_id' => $cabangLain->id,
            'user_id' => $userCabangLain->id,
            'total_transaksi' => 900000,
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 2
                && (float) $statistik['omzet_saya'] === 300000.0
                && $statistik['transaksi_cabang'] === 3
                && (float) $statistik['omzet_cabang'] === 600000.0;
        });
    }

    /* -----------------------------------------------------------------
     | TRANSAKSI TERBARU
     |------------------------------------------------------------------ */

    public function test_dashboard_displays_only_five_latest_transactions(): void
    {
        $tanggal = '2026-10-26';
        $referensiDibuat = [];

        for ($i = 1; $i <= 7; $i++) {
            $transaksi = $this->buatTransaksi([
                'tanggal' => $tanggal,
                'no_referensi' => 'CS-LATEST-' . $i,
                'nama_pelanggan' => 'Pelanggan ' . $i,
                'created_at' => Carbon::parse($tanggal . ' 09:00:00')->addMinutes($i),
            ]);

            $referensiDibuat[] = $transaksi->no_referensi;
        }

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('transaksiTerbaru', function ($transaksiTerbaru) use ($referensiDibuat): bool {
            return $transaksiTerbaru->count() === 5
                && $transaksiTerbaru->pluck('no_referensi')->all() === array_reverse(array_slice($referensiDibuat, -5));
        });
    }

    public function test_latest_transactions_include_other_cs_in_same_branch(): void
    {
        $csLain = $this->buatUser(
            'Customer Service',
            $this->cabangUtama,
            'CS Rekan'
        );
        $tanggal = '2026-10-27';

        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'nama_pelanggan' => 'Pelanggan CS Utama',
        ]);
        $this->buatTransaksi([
            'tanggal' => $tanggal,
            'user_id' => $csLain->id,
            'nama_pelanggan' => 'Pelanggan CS Rekan',
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertViewHas('transaksiTerbaru', function ($transactions): bool {
            return $transactions->count() === 2
                && $transactions->pluck('nama_pelanggan')->contains('Pelanggan CS Utama')
                && $transactions->pluck('nama_pelanggan')->contains('Pelanggan CS Rekan');
        });
    }

    public function test_latest_transactions_load_user_relationship(): void
    {
        $this->buatTransaksi([
            'tanggal' => '2026-10-28',
            'nama_pelanggan' => 'Pelanggan Relasi',
        ]);

        $response = $this->get(route('cs.dashboard', ['tanggal' => '2026-10-28']));

        $response->assertOk();
        $response->assertViewHas('transaksiTerbaru', function ($transactions): bool {
            $transaksi = $transactions->first();

            return $transaksi !== null
                && $transaksi->relationLoaded('user')
                && $transaksi->user?->name === 'CS Utama';
        });
    }

    public function test_dashboard_renders_empty_state_when_no_transactions_exist(): void
    {
        $tanggal = '2026-10-29';

        $response = $this->get(route('cs.dashboard', ['tanggal' => $tanggal]));

        $response->assertOk();
        $response->assertSee('Belum ada transaksi');
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 0
                && (float) $statistik['omzet_saya'] === 0.0
                && $statistik['transaksi_cabang'] === 0
                && (float) $statistik['omzet_cabang'] === 0.0;
        });
        $response->assertViewHas('transaksiTerbaru', fn ($data) => $data->isEmpty());
    }

    public function test_dashboard_displays_branch_name(): void
    {
        $response = $this->get(route('cs.dashboard'));

        $response->assertOk();
        $response->assertViewHas('namaCabang', 'Cabang Utama');
        $response->assertSee('Cabang Utama');
    }

    public function test_dashboard_uses_fallback_when_customer_service_has_no_branch(): void
    {
        $csTanpaCabang = $this->buatUser(
            'Customer Service',
            null,
            'CS Tanpa Cabang'
        );

        $response = $this->actingAs($csTanpaCabang)->get(route('cs.dashboard'));

        $response->assertOk();
        $response->assertViewHas('namaCabang', 'Belum ditentukan');
        $response->assertViewHas('statistik', function (array $statistik): bool {
            return $statistik['transaksi_saya'] === 0
                && $statistik['omzet_saya'] == 0
                && $statistik['transaksi_cabang'] === 0
                && $statistik['omzet_cabang'] == 0;
        });
    }

    /* -----------------------------------------------------------------
     | HELPER PEMBUATAN DATA TEST
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

    private function buatUser(string $role, ?Cabang $cabang, string $nama): User
    {
        return User::factory()->create([
            'name' => $nama,
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatTransaksi(array $attributes = []): Transaksi
    {
        $this->nomorTransaksi++;

        $createdAt = $attributes['created_at'] ?? null;
        unset($attributes['created_at']);

        $transaksi = Transaksi::create(array_merge([
            'no_referensi' => 'CS-TEST-' . str_pad((string) $this->nomorTransaksi, 5, '0', STR_PAD_LEFT),
            'tanggal' => date('Y-m-d'),
            'nama_pelanggan' => 'Pelanggan Test ' . $this->nomorTransaksi,
            'nomor_hp' => '081234567890',
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->customerService->id,
            'total_transaksi' => 100000,
            'total_hpp' => 40000,
        ], $attributes));

        if ($createdAt !== null) {
            $transaksi->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        return $transaksi;
    }
}
