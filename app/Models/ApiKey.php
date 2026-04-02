<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * ApiKey — API Key để user truy cập DBaaS qua SDK/REST API.
 *
 * Key được hash (SHA-256) trước khi lưu — tương tự cách xử lý API token.
 * Chỉ hiển thị 8 ký tự cuối trên UI (ndh_••••••••a1b2c3d4).
 */
class ApiKey extends Model
{
    // ──── Constants ────
    public const KEY_PREFIX = 'ndh_';

    protected $fillable = [
        'user_id',
        'name',
        'key_hash',
        'key_last8',
        'last_used_at',
        'is_active',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'is_active'    => 'boolean',
    ];

    /**
     * Ẩn hash khỏi serialization.
     */
    protected $hidden = ['key_hash'];

    // ──── Relationships ────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ──── Static Methods ────

    /**
     * Tạo key mới (trả về plain key — chỉ hiện 1 lần duy nhất).
     *
     * @return array{key: string, hash: string, last8: string}
     */
    public static function generateKey(): array
    {
        $plainKey = self::KEY_PREFIX . Str::random(40);
        return [
            'key'   => $plainKey,
            'hash'  => hash('sha256', $plainKey),
            'last8' => substr($plainKey, -8),
        ];
    }

    /**
     * Tìm API key bằng plain key (hash rồi so sánh).
     */
    public static function findByKey(string $plainKey): ?self
    {
        $hash = hash('sha256', $plainKey);
        return static::where('key_hash', $hash)
            ->where('is_active', true)
            ->first();
    }

    // ──── Helper Methods ────

    /**
     * Hiển thị key đã mask cho UI: ndh_••••••••a1b2c3d4
     */
    public function getMaskedKey(): string
    {
        return self::KEY_PREFIX . '••••••••' . $this->key_last8;
    }

    /**
     * Cập nhật thời gian sử dụng lần cuối.
     */
    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Vô hiệu hóa key.
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
