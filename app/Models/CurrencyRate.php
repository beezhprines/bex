<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyRate extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'date', 'rate', 'currency_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public static function exchange(string $date, Currency $currency, float $amount = null)
    {
        if (empty($amount)) return $amount;

        return (self::firstWhere([
            'date' => $date,
            'currency_id' => $currency->id
        ])->rate ?? 0) * $amount;
    }
}
