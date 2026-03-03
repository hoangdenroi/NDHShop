<?php

namespace App\Models;

class Setting extends BaseModel
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'array',
        'is_deleted' => 'boolean',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy setting của user theo key
     */
    public static function getForUser(int $userId, string $key, mixed $default = null): mixed
    {
        $setting = static::where('user_id', $userId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Lưu setting cho user
     */
    public static function setForUser(int $userId, string $key, mixed $value, ?string $description = null): static
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }

    /**
     * Lấy setting hệ thống (không thuộc user nào)
     */
    public static function getSystem(string $key, mixed $default = null): mixed
    {
        $setting = static::whereNull('user_id')->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
