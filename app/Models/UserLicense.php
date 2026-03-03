<?php

namespace App\Models;

class UserLicense extends BaseModel
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'license_key',
        'granted_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
