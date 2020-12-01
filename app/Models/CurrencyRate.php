<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyRate extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        "date", "rate", "currency_id"
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public static function exchange(string $date, Currency $currency, float $amount = null)
    {
        if (empty($amount)) return $amount;

        return (self::firstWhere([
            "date" => $date,
            "currency_id" => $currency->id
        ])->rate ?? 0) * $amount;
    }

    public static function findByCurrencyAndDate(Currency $currency, string $date)
    {
        return self::firstWhere([
            "currency_id" => $currency->id,
            "date" => $date
        ]);
    }

    public static function seed(array $data)
    {
        $date = date(config("app.iso_date"));
        $rates = $data["rates"];
        $kzt = $rates["KZT"];

        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $rate = $rates[$currency->code] ?: $kzt;

            $currencyRate = self::findByCurrencyAndDate($currency, $date);

            if (!empty($currencyRate)) continue;

            $currencyRate = self::create([
                "date" => $date,
                "currency_id" => $currency->id,
                "rate" => round($kzt / $rate, 2)
            ]);
        }

        note("info", "currencyRate:seed", "Загружены валюты на дату {$date}", CurrencyRate::class);
    }
}
