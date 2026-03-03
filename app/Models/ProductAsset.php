<?php

namespace App\Models;

class ProductAsset extends BaseModel
{
    protected $fillable = [
        'product_id',
        'type',
        'url_or_path',
        'file_size',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
