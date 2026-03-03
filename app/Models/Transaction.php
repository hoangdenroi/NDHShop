<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'user_identifier',
        'amount',
        'fee',
        'net_amount',
        'currency',
        'transaction_no',
        'gateway_transaction_id',
        'bank_code',
        'status',
        'payment_method',
        'response_code',
        'order_info',
        'pay_date',
        'account_number',
        'failure_reason',
        'refunded_amount',
        'refunded_at',
        'refund_reason',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'pay_date' => 'datetime',
        'refunded_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'amount' => 'integer',
        'fee' => 'integer',
        'net_amount' => 'integer',
        'refunded_amount' => 'integer',
    ];

    /**
     * User thực hiện giao dịch (nullable - guest checkout)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Danh sách các đơn hàng gắn với giao dịch này
     */
    public function orders()
    {
        // Chú ý: Đảm bảo bạn đã có model Order
        return $this->hasMany(\App\Models\Order::class);
    }
}
