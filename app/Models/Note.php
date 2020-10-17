<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory, ModelBase;

    protected $fillable = [
        'level', 'code', 'message', 'description', 'model', 'model_id'
    ];
}
