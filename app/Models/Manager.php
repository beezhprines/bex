<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'premium_rate', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
