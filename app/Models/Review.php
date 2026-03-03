<?php

namespace App\Models;

class Review extends BaseModel
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
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
}
