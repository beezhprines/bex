<?php

namespace App\Models;

trait ModelBase
{
    public static function findByCode(string $code)
    {
        return static::withTrashed()->firstWhere('code', $code);
    }
}
