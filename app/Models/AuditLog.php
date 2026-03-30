<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

class AuditLog extends BaseModel
{
    use Prunable;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the prunable model query.
     * Xóa các log cũ hơn 90 ngày.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }
}
