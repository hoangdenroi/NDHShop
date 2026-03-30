<?php

namespace App\Models;

/**
 * GiftAsset — Tài nguyên media cho quà tặng.
 *
 * Lưu URL các file media (ảnh động, nhạc, video, Lottie...)
 * phân loại theo GiftCategory.
 */
class GiftAsset extends BaseModel
{
    protected $fillable = [
        'category_id',
        'name',
        'type',
        'url',
        'thumbnail',
        'file_size',
        'description',
        'tags',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'tags'        => 'array',
        'is_active'   => 'boolean',
        'is_deleted'  => 'boolean',
        'sort_order'  => 'integer',
        'category_id' => 'integer',
    ];

    /**
     * Loại assets hợp lệ.
     */
    public const TYPES = [
        'image'  => 'Ảnh',
        'gif'    => 'Ảnh động (GIF)',
        'audio'  => 'Âm thanh',
        'video'  => 'Video',
        'lottie' => 'Lottie Animation',
    ];

    // ──── Relationships ────

    public function category()
    {
        return $this->belongsTo(GiftCategory::class, 'category_id');
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ──── Accessors ────

    /**
     * Lấy label hiển thị cho type.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Lấy tên category hiển thị.
     */
    public function getCategoryLabelAttribute(): string
    {
        return $this->category?->name ?? 'Không phân loại';
    }
}
