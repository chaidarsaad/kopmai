<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Product extends Model
{

    protected $fillable = [
        'name',
        'category_id',
        'shop_id',
        'description',
        'modal',
        'price',
        'laba',
        'stock',
        'is_active',
        'image',
    ];

    public function setLabaAttribute()
    {
        $this->attributes['laba'] = $this->attributes['price'] - $this->attributes['modal'];
    }

    public function getImageUrlAttribute()
    {
        return 'https://drive.google.com/thumbnail?id=' . $this->image . '&sz=w1000';
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Jika modal kosong, maka laba = price
            if (!is_null($product->price) && is_null($product->modal)) {
                $product->laba = $product->price;
            }

            // Jika price dan modal tersedia, maka laba = price - modal
            if (!is_null($product->price) && !is_null($product->modal)) {
                $product->laba = $product->price - $product->modal;
            }

            // Set is_active menjadi false jika price kosong
            if (empty($product->price)) {
                $product->is_active = false;
            }
        });
    }


    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!isset($this->attributes['slug'])) {
            $this->attributes['slug'] = str()->slug($value . '-' . str()->uuid());
        }
    }

    public static function search($keyword)
    {
        return self::where('name', 'like', '%' . $keyword . '%')->get();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
