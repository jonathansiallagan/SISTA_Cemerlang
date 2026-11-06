<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class AdminLayoutStisla extends Component
{
    public $user;
    public array $menuSections;
    public string $avatarUrl;

    // Properti $theme telah dihapus karena Tailwind CSS menangani styling langsung di Blade

    public function __construct()
    {
        $this->user = Auth::user();

        // Set Avatar URL secara mandiri tanpa bergantung pada class lama
        $this->avatarUrl = $this->user->avatar_url ?? ($this->user->avatar ? asset('storage/' . $this->user->avatar) : asset('stisla/assets/img/avatar/avatar-1.png'));

        // Definisi Menu Lengkap berdasarkan Role
        $this->menuSections = [
            [
                'title' => 'Menu Utama',
                'visible' => true,
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => $this->user->role === 'Customer Service' ? 'cs.dashboard' : 'admin.dashboard',
                        'icon' => 'fa-fire',
                        'visible' => true,
                    ],
                    [
                        'label' => 'Transaksi',
                        'route' => 'transaksi.index',
                        'icon' => 'fa-receipt',
                        'visible' => true,
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'route' => 'pengeluaran.index',
                        'icon' => 'fa-money-bill-wave',
                        'visible' => true,
                    ],[
                        'label' => 'Produk',
                        'route' => 'produk.index',
                        'icon' => 'fa-box-open',
                        'visible' => true,
                    ],[
                        'label' => 'Jurnal Umum',
                        'route' => 'jurnal.index',
                        'icon' => 'fa-book-open',
                        'visible' => true,
                    ],[
                        'label' => 'Buku Besar',
                        'route' => 'buku_besar.index',
                        'icon' => 'fa-book',
                        'visible' => true,
                    ],[
                        'label' => 'Laba Rugi',
                        'route' => 'laporan_laba_rugi.index',
                        'icon' => 'fa-chart-pie',
                        'visible' => true,
                    ],
                ]
            ],
            [
                'title' => 'Persediaan',
                'visible' => in_array($this->user->role, ['Admin Pusat', 'Admin Cabang']),
                'items' => [
                    
                    [
                        'label' => 'Bahan Baku',
                        'route' => 'bahan-baku.index',
                        'icon' => 'fa-boxes',
                        'visible' => true,
                    ],
                    [
                        'label' => 'Persediaan',
                        'route' => 'persediaan.index',
                        'icon' => 'fa-warehouse',
                        'visible' => true,
                    ],
                ]
            ],
            [
                'title' => 'Master Data',
                'visible' => $this->user->role === 'Admin Pusat',
                'items' => [
                    [
                        'label' => 'Cabang',
                        'route' => 'cabang.index',
                        'icon' => 'fa-store',
                        'visible' => true,
                    ],
                    [
                        'label' => 'User',
                        'route' => 'user.index',
                        'icon' => 'fa-users',
                        'visible' => true,
                    ],
                    [
                        'label' => 'Akun',
                        'route' => 'akun.index',
                        'icon' => 'fa-list-ol',
                        'visible' => true,
                    ],
                ]
            ]
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.admin-layout-stisla');
    }
}
