<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class NotFound extends Component
{
    public function mount()
    {
        $this->about = Store::first();
    }
    public function render()
    {
        return view('errors.404');
    }
}
