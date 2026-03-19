<?php

namespace App\Models;

/**
 * GiftOrder — Đơn hàng thanh toán cho gift pages.
 *
 * Mỗi khi user chọn plan (Basic/Premium) và thanh toán,
 * một GiftOrder sẽ được tạo để lưu lịch sử giao dịch.
 */
class GiftOrder extends BaseModel
{
    // ──── Constants ────

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const PLAN_BASIC   = 'basic';
    public const PLAN_PREMIUM = 'premium';

    public const METHOD_BALANCE = 'balance';
    public const METHOD_VNPAY   = 'vnpay';
    public const METHOD_MOMO    = 'momo';

    protected $fillable = [
        'user_id',
        'gift_page_id',
        'order_code',
        'plan',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount'     => 'integer',
        'paid_at'    => 'datetime',
        'metadata'   => 'array',
        'is_deleted' => 'boolean',
    ];

    // ──── Relationships ────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giftPage()
    {
        return $this->belongsTo(GiftPage::class);
    }

    // ──── Helper Methods ────

    /**
     * Đánh dấu đơn hàng đã thanh toán thành công.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status'  => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    /**
     * Kiểm tra đã thanh toán chưa.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Tạo mã đơn hàng ngẫu nhiên.
     */
    public static function generateOrderCode(): string
    {
        return 'GO-' . strtoupper(\Illuminate\Support\Str::random(8));
    }

    // ──── Scopes ────

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
