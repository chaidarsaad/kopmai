<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone_number',
        'nama_santri',
    ];

    protected static function booted()
    {
        static::updating(function ($user) {
            if (
                ($user->email === 'chaidaar@genzproject.my.id' || $user->id === 1)
                && $user->isDirty('is_admin') && $user->is_admin == 0
            ) {

                $user->is_admin = $user->getOriginal('is_admin');
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // return (bool) $this->is_admin;

        if ($this->roles->isEmpty()) {
            return false;
        }

        return true;
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
