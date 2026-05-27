<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'referral_code',
        'referred_by',
        'referral_rewarded',
        'is_verified',
        'verification_status',
        'balance',
        'has_initial_deposit',
        'initial_deposit_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'           => 'datetime',
            'initial_deposit_confirmed_at' => 'datetime',
            'password'                    => 'hashed',
            'is_verified'                 => 'boolean',
            'has_initial_deposit'         => 'boolean',
            'referral_rewarded'           => 'boolean',
            'balance'                     => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateReferralCode($user->name);
            }
        });
    }

    private static function generateReferralCode(string $name): string
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $base = substr($base ?: 'user', 0, 10);

        do {
            $code = $base . '_' . strtolower(Str::random(4));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function idVerification(): HasOne
    {
        return $this->hasOne(IdVerification::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class, 'created_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function balanceBreakdown(): array
    {
        $profit   = (float) $this->transactions()->where('type', 'profit')->where('status', 'success')->sum('amount');
        $referral = (float) $this->transactions()->where('type', 'referral_reward')->where('status', 'success')->sum('amount');

        return [
            'investment_profit' => $profit,
            'referral_reward'   => $referral,
        ];
    }
}
