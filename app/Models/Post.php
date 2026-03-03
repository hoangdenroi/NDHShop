<?php

namespace App\Models;

class Post extends BaseModel
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'thumbnail',
        'summary',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'published_at',
        'view_count'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }
}
