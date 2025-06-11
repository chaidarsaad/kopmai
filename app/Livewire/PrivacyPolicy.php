<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    public $store;
    public function mount()
    {
        $this->store = Store::first();
    }
    public function render()
    {
        return view('livewire.privacy-policy');
    }
}
