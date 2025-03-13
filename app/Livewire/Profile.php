<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;

class Profile extends Component
{
    public $name;
    public $email;
    public $whatsapp;
    public $is_admin;

    public function render()
    {
        return view('livewire.profile');
    }

    public function mount()
    {
        $user = auth()->user();
        $this->whatsapp = Store::first()->whatsapp;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('home');
    }
}
