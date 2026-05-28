<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        $flush = fn (self $setting) => Cache::forget("system_setting_{$setting->key}");

        static::saved($flush);
        static::deleted($flush);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $data = Cache::remember("system_setting_{$key}", 3600, function () use ($key) {
            $row = static::where('key', $key)->first();
            return $row ? ['value' => $row->value, 'type' => $row->type] : null;
        });

        if (!$data) {
            return $default;
        }

        return match ($data['type']) {
            'integer' => (int) $data['value'],
            'float', 'decimal' => (float) $data['value'],
            'boolean' => filter_var($data['value'], FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($data['value'], true),
            default => $data['value'],
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type]
        );

        Cache::forget("system_setting_{$key}");

        return $setting;
    }
}
