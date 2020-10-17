<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'title', 'code', 'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
