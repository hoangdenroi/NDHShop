<?php

namespace App\Models;

class Wishlist extends BaseModel
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * The user that owns the wishlist item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The product that is favorited.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
