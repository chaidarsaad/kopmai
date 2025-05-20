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
        $currentRoute = request()->route()->getName();

        return match ($currentRoute) {
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
