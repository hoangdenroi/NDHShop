<?php

namespace App\Models;

/**
 * GiftTemplate — Mẫu template quà tặng.
 * 
 * Lưu mã nguồn HTML/CSS/JS dạng Base64 và JSON schema
 * để định nghĩa form nhập liệu cho user.
 */
class GiftTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'category_id',
        'html_code',
        'css_code',
        'js_code',
        'schema',
        'is_active',
        'is_premium',
        'price',
        'usage_count',
    ];

    protected $casts = [
        'schema'     => 'array',
        'is_active'  => 'boolean',
        'is_premium' => 'boolean',
        'is_deleted'  => 'boolean',
        'category_id' => 'integer',
        'price'       => 'integer',
        'usage_count' => 'integer',
    ];

    // ──── Relationships ────

    /**
     * Category (chủ đề) của template.
     */
    public function category()
    {
        return $this->belongsTo(GiftCategory::class, 'category_id');
    }

    public function giftPages()
    {
        return $this->hasMany(GiftPage::class, 'template_id');
    }

    // ──── Accessors: Decode Base64 khi đọc ────

    public function getDecodedHtmlAttribute(): string
    {
        return base64_decode($this->html_code ?? '');
    }

    public function getDecodedCssAttribute(): ?string
    {
        return $this->css_code ? base64_decode($this->css_code) : null;
    }

    public function getDecodedJsAttribute(): ?string
    {
        return $this->js_code ? base64_decode($this->js_code) : null;
    }

    // ──── Helper: Encode Base64 khi lưu ────

    /**
     * Encode code sang Base64 trước khi lưu.
     * Dùng khi store/update từ Controller.
     */
    public static function encodeCode(string $rawCode): string
    {
        return base64_encode($rawCode);
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    // ──── Helper Methods ────

    /**
     * Lấy danh sách fields từ schema.
     */
    public function getSchemaFields(): array
    {
        return $this->schema['fields'] ?? [];
    }

    /**
     * Render template: thay thế placeholders bằng data thực.
     * {{KEY}} → value từ $data
     */
    public function renderHtml(array $data): string
    {
        $html = $this->decoded_html;

        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', e($value ?? ''), $html);
        }

        return $html;
    }

    /**
     * Tăng số lần sử dụng.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Lấy tên category hiển thị thông qua relationship.
     */
    public function getCategoryLabelAttribute(): string
    {
        return $this->category?->name ?? 'Không xác định';
    }
}
