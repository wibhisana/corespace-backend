<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    private const CACHE_TTL_MINUTES = 60;

    public static function findToken($token)
    {
        if (! str_contains($token, '|')) {
            return parent::findToken($token);
        }

        [$id, $tokenString] = explode('|', $token, 2);

        $instance = Cache::remember(
            self::cacheKey($id),
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => static::query()->find($id)
        );

        if (! $instance) {
            return null;
        }

        if (! hash_equals($instance->token, hash('sha256', $tokenString))) {
            return null;
        }

        if ($instance->expires_at && $instance->expires_at->isPast()) {
            Cache::forget(self::cacheKey($id));
            return null;
        }

        return $instance;
    }

    protected static function booted(): void
    {
        // Sanctum bumps last_used_at on every authenticated request. Forgetting
        // the cache on every update would defeat the whole purpose, so we
        // write-through the fresh model state instead.
        static::updated(function (self $token) {
            Cache::put(
                self::cacheKey($token->id),
                $token,
                now()->addMinutes(self::CACHE_TTL_MINUTES)
            );
        });

        static::deleted(function (self $token) {
            Cache::forget(self::cacheKey($token->id));
        });
    }

    private static function cacheKey(int|string $id): string
    {
        return "sanctum:pat:{$id}";
    }
}
