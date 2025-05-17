<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_active',
        'username_telegram',
        'acc_bank',
        'is_ongkir',
        'ongkir'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str()->slug($value);
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($shop) {
            if ($shop->is_active) {
                $shop->products()->where('is_active', false)->update(['is_active' => true]);
            } else {
                $shop->products()->update(['is_active' => false]);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
