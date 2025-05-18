<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;

class Profile extends Component
{
    public $name;
    public $email;
    public $whatsapp;
    public $is_pengelola;

    public function mount()
    {
        $user = auth()->user();
        $this->whatsapp = Store::first()->whatsapp;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_pengelola = $user->hasAnyRole(['pengelola_web', 'owner_tenant']);
    }

    public function render()
    {
        return view('livewire.profile');
    }


    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('home');
    }
}
