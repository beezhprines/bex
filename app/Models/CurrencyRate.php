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
}
