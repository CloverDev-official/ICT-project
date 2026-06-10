<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $table = 'setting';

    protected $fillable = [
        'key',
        'value'
    ];

    public static function values(): array
    {
        return static::query()->pluck('value', 'key')->all();
    }

    public static function valueOf(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function upsertValue(string $key, string $value): void
    {
        static::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value,
        ]);
    }

    public static function resolveAssetUrl(?string $value, ?string $fallback = null): string
    {
        $value = $value ?: $fallback;

        if (!$value) {
            return '';
        }

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        if (Str::startsWith($value, 'assets/')) {
            return '/' . ltrim($value, '/');
        }

        if (!Storage::disk('public')->exists($value) && $fallback && $fallback !== $value) {
            return static::resolveAssetUrl($fallback);
        }

        return '/storage/' . ltrim($value, '/');
    }
}
