<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminLayout extends Component
{
    // 1. Deklarasi Public Properties agar otomatis terbaca di Blade (Mengatasi error Undefined variable)
    // 2. Tambahkan PHPDoc @var agar Intelephense mengenali class User milikmu (Mengatasi error Undefined method)
    
    /** @var \App\Models\User */
    public $user;
    
    public array $menuSections;
    public string $avatarUrl;
    public array $theme;

    /**
     * Constructor berjalan pertama kali saat <x-admin-layout> dipanggil.
     */
    public function __construct()
    {
        // Paksa cast (type-hint) Auth::user() menjadi model User spesifik
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        $this->user = $currentUser;
        
        // Inisialisasi variabel lainnya
        $this->theme = [
            'linkBase'   => 'flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200',
            'linkActive' => 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25',
            'linkIdle'   => 'text-slate-400 hover:bg-white/10 hover:text-white',
        ];
        
        $this->menuSections = $this->generateMenuSections();
        $this->avatarUrl    = $this->generateAvatarUrl();
    }

    /**
     * Logika untuk menghasilkan struktur array menu dinamis.
     */
    
    private function generateMenuSections(): array
    {
        $isAdmin = $this->user->isAdmin();
        $isAdminPusat = $this->user->role === 'Admin Pusat';

        return [
            [
                'title'   => 'Menu Utama',
                'dot'     => 'bg-blue-500',
                'visible' => true,
                'items'   => [
                    [
                        'label'  => 'Dashboard',
                        'icon'   => 'fa-chart-pie',
                        'route'  => $this->user->homeRoute(),
                        'active' => request()->is('admin', 'admin/*', 'cs', 'cs/*'),
                    ],
                    [
                        'label' => 'Transaksi',
                        'icon'  => 'fa-exchange-alt',
                        'route' => 'transaksi.index',
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'icon'  => 'fa-wallet',
                        'route' => 'pengeluaran.index',
                    ],
                    [
                        'label'   => 'Produk',
                        'icon'    => 'fa-box',
                        'route'   => 'produk.index',
                        'visible' => $isAdmin,
                    ],
                    [
                        'label'   => 'Jurnal Umum',
                        'icon'    => 'fa-book',
                        'route'   => 'jurnal.index',
                        'visible' => $isAdmin,
                    ],
                    [
                        'label'   => 'Buku Besar',
                        'icon'    => 'fa-book-open',
                        'route'   => 'buku_besar.index',
                        'visible' => $isAdmin,
                    ],
                    [
                        'label'   => 'Laporan Laba Rugi',
                        'icon'    => 'fa-chart-line',
                        'route'   => 'laporan_laba_rugi.index',
                        'visible' => $isAdmin,
                    ],
                ],
            ],

            // KATEGORI PERSEDIAAN
            [
                'title'   => 'Persediaan',
                'dot'     => 'bg-amber-400',
                'visible' => $isAdmin,
                'items'   => [
                    [
                        'label' => 'Bahan Baku',
                        'icon'  => 'fa-boxes',
                        'route' => 'bahan-baku.index',
                    ],
                    [
                        'label' => 'Persediaan',
                        'icon'  => 'fa-warehouse',
                        'route' => 'persediaan.index',
                    ],
                ],
            ],

            [
                'title'   => 'Master Data',
                'dot'     => 'bg-emerald-400',
                'visible' => $isAdminPusat,
                'items'   => [
                    [
                        'label' => 'Cabang',
                        'icon'  => 'fa-store',
                        'route' => 'cabang.index',
                    ],
                    [
                        'label' => 'User',
                        'icon'  => 'fa-users',
                        'route' => 'user.index',
                    ],
                    [
                        'label' => 'Akun',
                        'icon'  => 'fa-list-ul',
                        'route' => 'akun.index',
                    ],
                ],
            ],
        ];
    }

    /**
     * Logika untuk menghasilkan URL Avatar.
     */
    private function generateAvatarUrl(): string
    {
        return $this->user->avatar
            ? asset('storage/' . $this->user->avatar)
            : 'https://ui-avatars.com/api/?' . http_build_query([
                'name'       => $this->user->name,
                'background' => 'E0F2FE',
                'color'      => '2563EB',
                'bold'       => 'true',
            ]);
    }

    /**
     * Render komponen ke view.
     * Tidak perlu melempar array [] di view(), karena public properties ($user, $menuSections, dll) sudah otomatis terkirim.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin-layout');
    }
}