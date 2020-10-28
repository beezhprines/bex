<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
