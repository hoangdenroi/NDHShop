<?php

namespace App\Models;

/**
 * GiftCategory — Danh mục quà tặng.
 *
 * Quản lý các chủ đề (Tết, Sinh nhật, Valentine...) cho Gift Templates.
 */
class GiftCategory extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_deleted' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ──── Relationships ────

    /**
     * Danh sách templates thuộc category này.
     */
    public function giftTemplates()
    {
        return $this->hasMany(GiftTemplate::class, 'category_id');
    }

    /**
     * Danh sách assets thuộc category này.
     */
    public function giftAssets()
    {
        return $this->hasMany(GiftAsset::class, 'category_id');
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sắp xếp theo sort_order tăng dần.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
