<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasUnitcode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUnitcode;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'unitcode',
        'phone',
        'avatar_url',
        'balance',
        'password',
        'google_id',
        'role',
        'status',
        'is_deleted',
        'last_change_password_at',
        'last_login_at',
        'theme',
        'notification',
        'language',
        // Cloud Plan
        'cloud_plan',
        'cloud_plan_billing_cycle',
        'cloud_plan_expires_at',
        'cloud_plan_grace_ends_at',
        'cloud_db_override',
        'cloud_storage_override',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_change_password_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'is_deleted' => 'boolean',
            'theme' => 'array',
            'notification' => 'array',
            // Cloud Plan
            'cloud_plan_expires_at' => 'datetime',
            'cloud_plan_grace_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the user's cart.
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the user's wishlists.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // ──── Cloud Plan Relationships ────

    public function cloudPlanOrders()
    {
        return $this->hasMany(CloudPlanOrder::class);
    }

    public function cloudDatabases()
    {
        return $this->hasMany(CloudDatabase::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    // ──── Cloud Plan Helpers ────

    /**
     * Lấy plan thực tế cho Database (override ưu tiên hơn gói chung).
     */
    public function getDbPlan(): string
    {
        return $this->cloud_db_override ?? $this->cloud_plan ?? 'free';
    }

    /**
     * Lấy plan thực tế cho Storage (override ưu tiên hơn gói chung).
     */
    public function getStoragePlan(): string
    {
        return $this->cloud_storage_override ?? $this->cloud_plan ?? 'free';
    }

    /**
     * Lấy quota config cho Database.
     */
    public function getDbQuota(): array
    {
        return config('cloud_plan.plans.' . $this->getDbPlan(), config('cloud_plan.plans.free'));
    }

    /**
     * Lấy quota config cho Storage.
     */
    public function getStorageQuota(): array
    {
        return config('cloud_plan.plans.' . $this->getStoragePlan(), config('cloud_plan.plans.free'));
    }

    /**
     * Gói cloud plan còn hiệu lực không.
     */
    public function isCloudPlanActive(): bool
    {
        if ($this->cloud_plan === 'free') {
            return true;
        }

        return $this->cloud_plan_expires_at && $this->cloud_plan_expires_at->isFuture();
    }

    /**
     * Đang trong grace period không.
     */
    public function isInGracePeriod(): bool
    {
        return $this->cloud_plan_grace_ends_at && $this->cloud_plan_grace_ends_at->isFuture();
    }

    /**
     * Gói cloud plan hiện tại có phải Free không.
     */
    public function isFreePlan(): bool
    {
        return $this->cloud_plan === 'free';
    }
}
