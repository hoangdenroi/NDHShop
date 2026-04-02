<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

/**
 * CloudDatabase — Metadata các database instance đã tạo.
 *
 * Mỗi instance tương ứng 1 database thật trên PostgreSQL/MySQL server.
 * Password được mã hóa AES (Crypt) trước khi lưu.
 */
class CloudDatabase extends BaseModel
{
    // ──── Constants: Engine ────
    public const ENGINE_POSTGRESQL = 'postgresql';
    public const ENGINE_MYSQL      = 'mysql';

    // ──── Constants: Status ────
    public const STATUS_PROVISIONING = 'provisioning';
    public const STATUS_ACTIVE       = 'active';
    public const STATUS_SUSPENDED    = 'suspended';
    public const STATUS_DELETING     = 'deleting';
    public const STATUS_DELETED      = 'deleted';

    // ──── Constants: Port mặc định theo engine ────
    public const DEFAULT_PORTS = [
        self::ENGINE_POSTGRESQL => 5432,
        self::ENGINE_MYSQL      => 3306,
    ];

    protected $fillable = [
        'user_id',
        'engine',
        'db_name',
        'db_user',
        'db_password_encrypted',
        'host',
        'port',
        'status',
        'max_connections',
        'active_connections',
        'max_storage_mb',
        'storage_used_mb',
        'last_activity_at',
        'expires_at',
    ];

    protected $casts = [
        'port'               => 'integer',
        'max_connections'    => 'integer',
        'active_connections' => 'integer',
        'max_storage_mb'     => 'integer',
        'storage_used_mb'    => 'decimal:2',
        'last_activity_at'   => 'datetime',
        'expires_at'         => 'datetime',
        'is_deleted'         => 'boolean',
    ];

    /**
     * Ẩn password encrypted khỏi serialization.
     */
    protected $hidden = ['db_password_encrypted'];

    // ──── Relationships ────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ──── Helper Methods ────

    /**
     * Lấy port mặc định theo engine.
     */
    public static function getDefaultPort(string $engine): int
    {
        return self::DEFAULT_PORTS[$engine] ?? 3306;
    }

    /**
     * Kiểm tra DB còn hoạt động không.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Kiểm tra DB đang bị tạm dừng.
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Engine có phải PostgreSQL không.
     */
    public function isPostgresql(): bool
    {
        return $this->engine === self::ENGINE_POSTGRESQL;
    }

    /**
     * Engine có phải MySQL không.
     */
    public function isMysql(): bool
    {
        return $this->engine === self::ENGINE_MYSQL;
    }

    /**
     * Giải mã password DB.
     */
    public function getDecryptedPassword(): string
    {
        return Crypt::decryptString($this->db_password_encrypted);
    }

    /**
     * Tạo connection string cho user.
     */
    public function getConnectionString(): string
    {
        $protocol = $this->isPostgresql() ? 'postgresql' : 'mysql';
        return sprintf(
            '%s://%s:****@%s:%d/%s',
            $protocol,
            $this->db_user,
            $this->host,
            $this->port,
            $this->db_name,
        );
    }

    // ──── Scopes ────

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeByEngine($query, string $engine)
    {
        return $query->where('engine', $engine);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: DB gói Free không hoạt động quá N ngày.
     */
    public function scopeInactive($query, int $days)
    {
        return $query->where('last_activity_at', '<=', now()->subDays($days));
    }
}
