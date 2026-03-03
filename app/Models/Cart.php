<?php

namespace App\Models;

class Cart extends BaseModel
{
    protected $fillable = [
        'user_id',
        'session_id'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
