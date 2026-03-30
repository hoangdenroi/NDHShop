<?php

namespace App\Models;

use Illuminate\Support\Str;

/**
 * GiftPage — Trang quà tặng user tạo ra.
 *
 * Flow SaaS:
 * 1. User tạo gift → status = draft (chưa có share_code)
 * 2. Chọn plan (basic/premium) → thanh toán
 * 3. Thanh toán thành công → status = active, generate share_code
 * 4. Link chia sẻ: /gifts/{share_code}
 */
class GiftPage extends BaseModel
{
    // ──── Constants ────

    public const STATUS_DRAFT    = 'draft';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_DISABLED = 'disabled';

    public const PLAN_BASIC   = 'basic';
    public const PLAN_PREMIUM = 'premium';

    // Giá các gói (VND)
    public const PLAN_PRICES = [
        self::PLAN_BASIC   => 0,      // Miễn phí
        self::PLAN_PREMIUM => 49000,  // 49,000đ
    ];

    // Thời hạn (ngày), null = vĩnh viễn
    public const PLAN_DURATIONS = [
        self::PLAN_BASIC   => 7,    // 7 ngày
        self::PLAN_PREMIUM => null, // Vĩnh viễn
    ];

    protected $fillable = [
        'share_code',
        'user_id',
        'template_id',
        'page_data',
        'meta_title',
        'meta_image',
        'status',
        'plan',
        'is_premium',
        'rendered_html',
        'view_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'page_data'     => 'array',
        'expires_at'    => 'datetime',
        'is_active'     => 'boolean',
        'is_premium'    => 'boolean',
        'is_deleted'    => 'boolean',
        'view_count'    => 'integer',
    ];

    // ──── Share Code Generation ────

    /**
     * Tạo share_code ngẫu nhiên 8 ký tự, đảm bảo unique.
     * Chỉ gọi khi gift được kích hoạt (sau thanh toán).
     */
    public static function generateShareCode(int $length = 8): string
    {
        do {
            $code = Str::random($length);
        } while (self::withoutGlobalScopes()->where('share_code', $code)->exists());

        return $code;
    }

    // ──── Relationships ────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(GiftTemplate::class, 'template_id');
    }

    public function giftOrder()
    {
        return $this->hasOne(GiftOrder::class)->latest();
    }

    public function giftOrders()
    {
        return $this->hasMany(GiftOrder::class);
    }

    public function analytics()
    {
        return $this->hasMany(GiftAnalytic::class);
    }

    // ──── Helper Methods ────

    /**
     * Kiểm tra link đã hết hạn chưa.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false; // Không có ngày hết hạn = vĩnh viễn (premium)
        }

        return $this->expires_at->isPast();
    }

    /**
     * Kiểm tra có phải gói premium không.
     */
    public function isPremium(): bool
    {
        return $this->is_premium || $this->plan === self::PLAN_PREMIUM;
    }

    /**
     * Kiểm tra gift có thể chỉnh sửa không.
     * Chỉ cho edit khi ở trạng thái draft, HOẶC gói PREMIUM (trong 72h).
     */
    public function canBeEdited(): bool
    {
        if ($this->status === self::STATUS_DRAFT) {
            return true;
        }

        // Nếu đã kích hoạt (active/pending...), chỉ cho phép sửa nếu là gói Premium và còn hạn 72h
        if ($this->isPremium()) {
            return $this->edit_hours_left > 0;
        }

        return false;
    }

    /**
     * Lấy mốc thời gian kích hoạt thiệp.
     */
    public function getActivatedAtAttribute()
    {
        return $this->giftOrder?->paid_at ?? $this->created_at;
    }

    /**
     * Lấy số giờ còn lại để sửa (dành cho gói Premium).
     */
    public function getEditHoursLeftAttribute(): int
    {
        if ($this->status === self::STATUS_DRAFT) {
            return 999; // Không giới hạn cho draft
        }
        
        $expiryTime = $this->activated_at->copy()->addHours(72);
        if ($expiryTime->isPast()) {
            return 0;
        }
        
        return now()->diffInHours($expiryTime);
    }

    /**
     * Kiểm tra gift có đang hoạt động không.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    /**
     * Tăng lượt xem.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Lấy URL chia sẻ đầy đủ.
     */
    public function getShareUrlAttribute(): string
    {
        if (!$this->share_code) {
            return '#'; // Chưa có share_code (draft)
        }
        return url("/gifts/{$this->share_code}");
    }

    /**
     * Lấy giá trị data theo key.
     */
    public function getDataValue(string $key, $default = null)
    {
        return $this->page_data[$key] ?? $default;
    }

    /**
     * Lấy label trạng thái để hiển thị.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT    => 'Nháp',
            self::STATUS_PENDING  => 'Chờ thanh toán',
            self::STATUS_ACTIVE   => $this->isExpired() ? 'Hết hạn' : 'Đang chạy',
            self::STATUS_EXPIRED  => 'Hết hạn',
            self::STATUS_DISABLED => 'Đã tắt',
            default               => 'Không xác định',
        };
    }

    /**
     * Lấy label gói dịch vụ để hiển thị.
     */
    public function getPlanLabelAttribute(): string
    {
        return $this->plan === self::PLAN_PREMIUM ? 'Premium' : 'Basic';
    }

    /**
     * Render template với dữ liệu đã nhập.
     */
    public function render(): string
    {
        return $this->template->renderHtml($this->page_data ?? []);
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByPlan($query, string $plan)
    {
        return $query->where('plan', $plan);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeExpiredAndActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }
}
