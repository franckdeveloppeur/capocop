<?php

namespace App\Models;

use App\Observers\UserObserver;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasUuids,AuthenticationLoggable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'role',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // Relationships
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function authLogs(): HasMany
    {
        return $this->hasMany(AuthLog::class);
    }

    public function referralCodes(): HasMany
    {
        return $this->hasMany(ReferralCode::class);
    }

    public function personalAccessTokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }

    // Helper methods
    public function sendPasswordReset(): void
    {
        // Implementation for password reset
    }

    public function generateApiToken(): string
    {
        return $this->createToken('api')->plainTextToken;
    }
}
