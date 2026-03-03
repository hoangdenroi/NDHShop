<?php

namespace App\Models;

class PostCategory extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
