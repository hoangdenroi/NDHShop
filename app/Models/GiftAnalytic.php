<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GiftAnalytic — Tracking chi tiết lượt truy cập cho gift premium.
 *
 * Mỗi lần có người truy cập gift premium, một bản ghi sẽ được tạo
 * để lưu thông tin IP, device, referer phục vụ thống kê.
 */
class GiftAnalytic extends Model
{
    public $timestamps = false; // Dùng visited_at thay cho timestamps

    protected $fillable = [
        'gift_page_id',
        'ip_address',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    // ──── Relationships ────

    public function giftPage()
    {
        return $this->belongsTo(GiftPage::class);
    }

    // ──── Scopes ────

    public function scopeByGift($query, int $giftPageId)
    {
        return $query->where('gift_page_id', $giftPageId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    /**
     * Tạo bản ghi analytics từ request hiện tại.
     */
    public static function track(GiftPage $giftPage): self
    {
        return self::create([
            'gift_page_id' => $giftPage->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'referer'      => request()->header('referer'),
            'visited_at'   => now(),
        ]);
    }
}
