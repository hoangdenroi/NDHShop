<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model hệ điều hành VPS
 * Dữ liệu sync từ Hetzner API GET /v1/images?type=system
 */
class VpsOperatingSystem extends Model
{
    protected $fillable = [
        'name',
        'hetzner_name',
        'os_flavor',
        'architecture',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Các gói VPS hỗ trợ HĐH này.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(VpsCategory::class, 'vps_category_operating_system');
    }

    /**
     * Scope: chỉ lấy HĐH đang active.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
