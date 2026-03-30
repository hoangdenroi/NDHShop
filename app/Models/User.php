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
}
