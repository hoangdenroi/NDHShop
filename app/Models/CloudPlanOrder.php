<?php

namespace App\Models;

use Illuminate\Support\Str;

/**
 * CloudPlanOrder — Đơn hàng thanh toán gói dịch vụ Cloud Plan.
 *
 * Mỗi khi user nâng cấp / gia hạn / downgrade,
 * một record sẽ được tạo để lưu lịch sử giao dịch (audit trail).
 */
class CloudPlanOrder extends BaseModel
{
    // ──── Constants: Action ────
    public const ACTION_UPGRADE   = 'upgrade';
    public const ACTION_RENEW     = 'renew';
    public const ACTION_DOWNGRADE = 'downgrade';
    public const ACTION_REFUND    = 'refund';

    // ──── Constants: Plan ────
    public const PLAN_FREE = 'free';
    public const PLAN_PRO  = 'pro';
    public const PLAN_MAX  = 'max';

    // ──── Constants: Billing Cycle ────
    public const CYCLE_MONTHLY    = 'monthly';
    public const CYCLE_QUARTERLY  = 'quarterly';
    public const CYCLE_SEMIANNUAL = 'semiannual';
    public const CYCLE_ANNUAL     = 'annual';

    protected $fillable = [
        'user_id',
        'order_code',
        'plan',
        'action',
        'billing_cycle',
        'months',
        'original_amount',
        'discount_percent',
        'amount',
        'balance_before',
        'balance_after',
        'starts_at',
        'expires_at',
        'note',
    ];

    protected $casts = [
        'months'           => 'integer',
        'original_amount'  => 'integer',
        'discount_percent' => 'integer',
        'amount'           => 'integer',
        'balance_before'   => 'decimal:2',
        'balance_after'    => 'decimal:2',
        'starts_at'        => 'datetime',
        'expires_at'       => 'datetime',
        'is_deleted'       => 'boolean',
    ];

    // ──── Relationships ────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ──── Helper Methods ────

    /**
     * Tạo mã đơn hàng duy nhất (prefix CP-).
     */
    public static function generateOrderCode(): string
    {
        return 'CP-' . strtoupper(Str::random(10));
    }

    /**
     * Kiểm tra đây có phải đơn hoàn tiền không.
     */
    public function isRefund(): bool
    {
        return $this->amount < 0;
    }

    // ──── Scopes ────

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
