<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model log hoạt động đơn hàng VPS
 * Ghi lại audit trail cho mọi action trên VPS
 */
class VpsOrderLog extends Model
{
    protected $fillable = [
        'vps_order_id',
        'user_id',
        'action',
        'detail',
        'amount',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    /**
     * Quan hệ: đơn hàng VPS.
     */
    public function vpsOrder(): BelongsTo
    {
        return $this->belongsTo(VpsOrder::class);
    }

    /**
     * Quan hệ: user thực hiện action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
