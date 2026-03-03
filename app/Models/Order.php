<?php

namespace App\Models;

class Order extends BaseModel
{
    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'payment_method',
        'payment_status',
        'total_amount',
        'note'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function licenses()
    {
        return $this->hasMany(UserLicense::class);
    }
}
