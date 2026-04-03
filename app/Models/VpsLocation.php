<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model vị trí datacenter VPS
 * Dữ liệu sync từ Hetzner API GET /v1/locations
 */
class VpsLocation extends Model
{
    protected $fillable = [
        'name',
        'hetzner_name',
        'city',
        'country',
        'network_zone',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Các gói VPS hỗ trợ Location này.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(VpsCategory::class, 'vps_category_location');
    }

    /**
     * Scope: chỉ lấy Location đang active.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
