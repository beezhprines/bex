<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'title', 'code'
    ];

    public function countries()
    {
        return $this->hasMany(Country::class);
    }

    public function currencyRates()
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function avgRate(string $startDate, string $endDate)
    {
        return CurrencyRate::whereBetween(DB::raw('DATE(date)'), array($startDate, $endDate))
            ->where('currency_id', $this->id)
            ->get()
            ->avg('rate');
    }
}
