<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investment extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'payment_type',
        'total_amount',
        'admin_fee',
        'tenure_months',
        'months_paid',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'tenure_months' => 'integer',
            'months_paid' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function installmentPayments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class)->orderBy('month_number');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'reference_id');
    }

    public function isInstallment(): bool
    {
        return $this->payment_type === 'installment';
    }

    public function isFull(): bool
    {
        return $this->payment_type === 'full';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function nextInstallment(): ?InstallmentPayment
    {
        return $this->installmentPayments()
            ->where('status', 'pending')
            ->orderBy('month_number')
            ->first();
    }
}
