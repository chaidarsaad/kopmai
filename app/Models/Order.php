<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'classroom_id',
        'subtotal',
        'total_amount',
        'status',
        'payment_status',
        'nama_santri',
        'recipient_name',
        'phone',
        'payment_gateway_transaction_id',
        'payment_gateway_data',
        'payment_proof',
        'notes',
    ];
    
       public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(PaymentMethod::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
