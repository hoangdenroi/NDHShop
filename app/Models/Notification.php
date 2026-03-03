<?php

namespace App\Models;

class Notification extends BaseModel
{
    protected $fillable = [
        'user_id',
        'scope',
        'title',
        'message',
        'type',
        'related_entity_type',
        'related_entity_id',
        'is_read',
        'read_at',
        'priority',
        'action_url',
        'data',
        'expires_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
