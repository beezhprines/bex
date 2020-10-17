<?php

namespace App\Models;

trait ModelBase
{
    public static function findByCode(string $code)
    {
        return static::withTrashed()->firstWhere('code', $code);
    }

    public static function findByOriginId(int $originId)
    {
        return static::withTrashed()->firstWhere('origin_id', $originId);
    }
}
