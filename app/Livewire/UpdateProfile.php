<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\{Auth, Hash};

class UpdateProfile extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;
    public $showPassword = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:users,name,' . Auth::id() . '|regex:/^(?! )[a-z\'’ ]+(?<! )$/',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone_number' => 'required|numeric',
            'password' => 'nullable|min:8|confirmed',
            'password_confirmation' => 'nullable|min:8'
        ];
    }

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'name.min' => 'Nama minimal 3 karakter',
        'name.unique' => 'Gagal, coba nama lain',
        'name.regex' => 'Nama hanya boleh mengandung huruf kecil, spasi ditengah, dan tanda kutip',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Gagal, coba email lain',
        'phone_number.required' => 'Nomor HP wajib diisi',
        'phone_number.numeric' => 'Nomor HP harus berupa angka',
        'password.min' => 'Password minimal 8 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'password_confirmation.min' => 'Konfirmasi Password minimal 8 karakter',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function updateProfile()
    {
        $validatedData = $this->validate($this->rules());

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        // set form password and password_confirmation to empty
        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('showAlert', [
            'message' => 'Berhasil disimpan',
            'type' => 'success'
        ]);
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('livewire.update-profile')->layout('components.layouts.app', ['hideBottomNav' => true]);
    }
}
