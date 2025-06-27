<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $fillable = [
        'uid',
        'image',
        'url',
        'is_show',
        'is_priority',
    ];

    protected static function booted()
    {
        static::creating(function (Carousel $model) {
            $model->uid = (string) str()->uuid();
        });

        static::saving(function (Carousel $model) {
            $model->uid = (string) str()->uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uid';
    }
}
