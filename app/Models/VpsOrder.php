<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

/**
 * Model đơn hàng VPS
 * Lưu thông tin đơn hàng + liên kết server Hetzner
 * root_password được mã hóa tự động bằng Laravel Crypt
 */
class VpsOrder extends Model
{
    protected $fillable = [
        'user_id',
        'vps_category_id',
        'order_code',
        'hetzner_server_id',
        'price',
        'duration_months',
        'operating_system',
        'location',
        'ip_address',
        'ipv6_address',
        'username',
        'root_password',
        'note',
        'admin_note',
        'status',
        'coupon_code',
        'discount_amount',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount_amount' => 'integer',
        'duration_months' => 'integer',
        'hetzner_server_id' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Mã hóa root_password khi lưu.
     */
    public function setRootPasswordAttribute(?string $value): void
    {
        $this->attributes['root_password'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Giải mã root_password khi đọc.
     */
    public function getRootPasswordAttribute(?string $value): ?string
    {
        if (!$value) return null;

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Quan hệ: user sở hữu đơn hàng.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ: gói VPS của đơn hàng.
     */
    public function vpsCategory(): BelongsTo
    {
        return $this->belongsTo(VpsCategory::class);
    }

    /**
     * Quan hệ: log hoạt động của đơn hàng.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(VpsOrderLog::class)->orderByDesc('created_at');
    }

    /**
     * Kiểm tra VPS đã hết hạn chưa.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Kiểm tra VPS đang hoạt động.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Kiểm tra có thể gia hạn không.
     */
    public function canRenew(): bool
    {
        return in_array($this->status, ['active']) && $this->vpsCategory?->is_renewable;
    }

    /**
     * Kiểm tra có thể hủy không.
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'active']);
    }

    /**
     * Tính số ngày còn lại.
     */
    public function daysRemaining(): int
    {
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    /**
     * Tính tổng số ngày của gói.
     */
    public function totalDays(): int
    {
        return $this->duration_months * 30;
    }

    /**
     * Tính số tiền hoàn khi hủy (theo tỷ lệ ngày còn lại).
     */
    public function refundAmount(): int
    {
        if ($this->status === 'pending') {
            return $this->price; // Hoàn 100% nếu chưa provision
        }

        $totalDays = $this->totalDays();
        if ($totalDays <= 0) return 0;

        $remaining = max(0, $this->daysRemaining());
        return (int) round($this->price * $remaining / $totalDays);
    }
}
