<?php

namespace App\Livewire\Auth;

use App\Models\Store;
use Livewire\Component;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Filament\Notifications\Actions\Action;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $showPassword = false;
    public $passwordConfirmationTouched = false;

    protected $rules = [
        'name' => 'required|min:3|unique:users,name|regex:/^(?! )[a-z\'’ ]+(?<! )$/',
        'email' => 'required|email|unique:users,email',
        // 'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required|min:8'
    ];
    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'name.min' => 'Nama minimal 3 karakter',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Pendaftaran gagal, coba email lain',
        'name.unique' => 'Pendaftaran gagal, coba nama lain',
        'password.required' => 'Password wajib diisi',
        'password_confirmation.required' => 'Konfirmasi Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password_confirmation.min' => 'Konfirmasi Password minimal 8 karakter',
        // 'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'name.regex' => 'Nama hanya boleh mengandung huruf kecil, spasi ditengah, dan tanda kutip',
    ];

    public function updated($propertyName)
    {
        if ($propertyName === 'password_confirmation') {
            $this->passwordConfirmationTouched = true;
        }

        if (
            $this->passwordConfirmationTouched &&
            $this->password_confirmation !== '' &&
            $this->password !== $this->password_confirmation
        ) {
            $this->addError('password', 'Password harus sama dengan konfirmasi password');
        } else {
            $this->resetValidation('password');
        }

        $this->validateOnly($propertyName);
    }


    public function register()
    {
        $this->name = strtolower(trim($this->name));

        $validateData = $this->validate();

        if (
            User::whereRaw('LOWER(name) = ?', [strtolower($validateData['name'])])->exists() ||
            User::where('email', $validateData['email'])->exists()
        ) {
            $this->addError('name', 'Pendaftaran gagal, coba nama lain');
            $this->addError('email', 'Pendaftaran gagal, coba email lain');
            return;
        }

        $isFirstUser = User::count() === 0;

        $user = User::create([
            'name' => strtolower(trim($validateData['name'])), // Pastikan tetap lowercase
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password']),
            'is_admin' => $isFirstUser
        ]);

        event(new Registered($user));

        Auth::login($user);

        $admins = User::where('is_admin', 1)->get()->unique('id');
        $title = 'Ada pengguna baru dengan nama: ' . $user->name;
        $body = 'email: ' . $user->email;
        // Notification::make()
        //     ->title($title)
        //     ->body($body)
        //     ->actions([
        //         Action::make('view')
        //             ->label('Lihat')
        //             ->url(fn() => route('filament.pengelola.resources.pengguna.index'))
        //             ->button()
        //             ->markAsRead(),
        //     ])
        //     ->sendToDatabase($admins);

        // return redirect()->intended($isFirstUser ? '/admin' : route('home'));
        return redirect()->intended(route('home'));
    }


    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }


    public function render()
    {
        return view('livewire.auth.register', [
            'store' => Store::first()
        ])->layout('components.layouts.app', ['hideBottomNav' => true, 'hideTopNav' => true]);
    }
}
