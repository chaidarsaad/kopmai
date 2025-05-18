<?php

namespace App\Livewire\Auth;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $name = '';
    public $password = '';
    public $showPassword = false;

    protected $rules = [
        'name' => 'required|min:3|regex:/^(?! )[a-z\'’ ]+(?<! )$/',
        'password' => 'required|min:8',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'name.min' => 'Nama minimal 3 karakter',
        'name.regex' => 'Nama hanya boleh mengandung huruf kecil, spasi ditengah, dan tanda kutip',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login()
    {
        $this->validate();

        $name = strtolower(trim($this->name));
        $password = $this->password;

        if (Auth::attempt(['name' => $name, 'password' => $password])) {
            session()->regenerate();

            $user = Auth::user();

            // if ($user->is_admin) {
            //     return redirect()->route('filament.pengelola.pages.dashboard');
            // }

            // return redirect()->intended(route('home'));
            if ($user->roles->isNotEmpty()) {
                return redirect()->route('filament.pengelola.pages.dashboard');
            }

            return redirect()->intended(route('home'));
        }

        $this->addError('name', 'Nama atau password salah');
        $this->password = '';
    }



    public function render()
    {
        return view('livewire.auth.login', [
            'store' => Store::first()
        ])->layout('components.layouts.app', ['hideBottomNav' => true]);
    }
}
