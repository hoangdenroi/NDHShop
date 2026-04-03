<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model gói VPS
 * Hỗ trợ 2 chế độ:
 *   - auto: tự động provision qua Hetzner Cloud API
 *   - manual: admin giao VPS thủ công
 */
class VpsCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'hetzner_server_type',
        'provision_type',
        'price',
        'annual_price',
        'cpu',
        'ram',
        'storage',
        'bandwidth',
        'is_renewable',
        'is_best_seller',
        'warranty',
        'description',
        'status',
        'sort_order',
        'sold_count',
    ];

    protected $casts = [
        'price' => 'integer',
        'annual_price' => 'integer',
        'is_renewable' => 'boolean',
        'is_best_seller' => 'boolean',
        'sort_order' => 'integer',
        'sold_count' => 'integer',
    ];

    /**
     * Các HĐH hỗ trợ cho gói này.
     */
    public function operatingSystems(): BelongsToMany
    {
        return $this->belongsToMany(VpsOperatingSystem::class, 'vps_category_operating_system');
    }

    /**
     * Các Location hỗ trợ cho gói này.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(VpsLocation::class, 'vps_category_location');
    }

    /**
     * Danh sách đơn hàng của gói này.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(VpsOrder::class);
    }

    /**
     * Scope: chỉ lấy gói đang active.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: lấy gói best seller.
     */
    public function scopeBestSeller($query)
    {
        return $query->where('is_best_seller', true);
    }

    /**
     * Lấy route key name bằng slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Kiểm tra gói dùng Hetzner auto-provision.
     */
    public function isAuto(): bool
    {
        return $this->provision_type === 'auto';
    }

    /**
     * Kiểm tra gói giao thủ công.
     */
    public function isManual(): bool
    {
        return $this->provision_type === 'manual';
    }
}
