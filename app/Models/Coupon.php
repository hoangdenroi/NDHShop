<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'min_order',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Kiểm tra mã giảm giá có hợp lệ không.
     */
    public function isValid(float $orderTotal = 0): bool
    {
        // Không hoạt động
        if (!$this->is_active) return false;

        // Chưa đến ngày bắt đầu
        if ($this->starts_at && now()->lt($this->starts_at)) return false;

        // Đã hết hạn
        if ($this->expires_at && now()->gt($this->expires_at)) return false;

        // Đã hết lượt sử dụng
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;

        // Đơn hàng chưa đạt mức tối thiểu
        if ($orderTotal < $this->min_order) return false;

        return true;
    }

    /**
     * Tính số tiền được giảm.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'percent') {
            $discount = $orderTotal * ($this->value / 100);
            // Áp dụng giới hạn giảm tối đa nếu có
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
            return round($discount, 2);
        }

        // Loại fixed: giảm trực tiếp, không vượt quá tổng đơn
        return min((float) $this->value, $orderTotal);
    }
}
