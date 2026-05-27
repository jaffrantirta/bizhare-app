<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Business extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'location',
        'image',
        'target_investors',
        'current_investors',
        'status',
        'activation_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'activation_date' => 'date',
            'target_investors' => 'integer',
            'current_investors' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);
            }
        });

        static::updating(function (Business $business) {
            if ($business->isDirty('name') && empty($business->slug)) {
                $business->slug = Str::slug($business->name);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function activeInvestments(): HasMany
    {
        return $this->hasMany(Investment::class)->where('status', 'active');
    }

    public function profitDistributions(): HasMany
    {
        return $this->hasMany(ProfitDistribution::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
