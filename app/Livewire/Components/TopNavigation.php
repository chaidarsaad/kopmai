<?php

namespace App\Livewire\Components;

use App\Models\Store;
use Livewire\Component;

class TopNavigation extends Component
{
    public $activeMenu;
    public $about;

    public function mount()
    {
        $this->about = Store::first();
        $this->activeMenu = $this->getActiveMenu();
    }

    public function getActiveMenu()
    {
        $route = request()->route();

        if (!$route) {
            return 'home'; // fallback jika route tidak ditemukan
        }

        return match ($route->getName()) {
            'home' => 'home',
            'shopping-cart' => 'shopping-cart',
            'orders' => 'orders',
            'profile' => 'profile',
            default => 'home'
        };
    }


    public function setActiveMenu($menu)
    {
        $this->activeMenu = $menu;
    }
    public function render()
    {
        return view('livewire.components.top-navigation');
    }
}
