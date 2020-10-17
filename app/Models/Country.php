<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'title', 'code', 'currency_id'
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
