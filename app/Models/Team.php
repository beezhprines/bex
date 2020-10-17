<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'title', 'premium_rate', 'operator_id', 'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function masters()
    {
        return $this->hasMany(Master::class);
    }

    public function currency()
    {
        return $this->city->country->currency ?? null;
    }
}
