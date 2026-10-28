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
    public array $theme;

    public function __construct()
    {
        $this->user = Auth::user();

        // Ambil konfigurasi menu dari komponen layout lama.
        $layout = new AdminLayout();

        $this->menuSections = $layout->menuSections;
        $this->avatarUrl = $layout->avatarUrl;

        // Class tema untuk Bootstrap/Stisla.
        $this->theme = [
            'linkBase' => 'nav-item',
            'linkActive' => 'active',
            'linkIdle' => '',
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.admin-layout-stisla');
    }
}