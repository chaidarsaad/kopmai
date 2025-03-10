<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'banner',
        'address',
        'whatsapp',
        'email_notification',
        'is_use_payment_gateway',
        'shipping_provider',
        'shipping_api_key',
        'shipping_area_id',
        'requires_customer_email_verification',
        'primary_color',
        'secondary_color',
        'shipping_courier',
        'is_open',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? Storage::url($this->banner) : null;
    }

    // casts
    protected $casts = [
        'is_open' => 'boolean',
    ];
}
