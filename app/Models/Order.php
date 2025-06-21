<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'student_id',
        'subtotal',
        'total_amount',
        'status',
        'payment_status',
        'nama_wali',
        'phone',
        'paid_date',
        'nominal_pembayaran',
        'payment_gateway_transaction_id',
        'payment_gateway_data',
        'payment_proof',
        'notes',
    ];

    // casts
    protected $casts = [
        'payment_gateway_data' => 'array',
        'payment_proof' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_date' => 'datetime',
        'subtotal' => 'integer',
        'total_amount' => 'integer',
        'status' => 'string',
        'payment_status' => 'string',
        'user_id' => 'integer',
        'student_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->user_id) {
                $order->user_id = Auth::id();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
