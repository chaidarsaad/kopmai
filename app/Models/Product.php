<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Product extends Model
{

    protected $fillable = [
        'name',
        'category_id',
        'shop_id',
        'description',
        'price',
        'buying_price',
        'stock',
        'is_active',
        'image',
        'images',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    protected $casts = [
        'images' => 'array',
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        return 'https://drive.google.com/thumbnail?id=' . $this->image . '&sz=w1000';
    }


    public function getFirstImageUrlAttribute()
    {
        $images = $this->images;

        if (is_array($images) && !empty($images)) {
            $reversed = array_reverse($images);
            return Storage::url($reversed[0]);
        }

        return null;
    }



    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
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
