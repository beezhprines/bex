<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'origin_id', 'title', 'price', 'comission', 'conversion', 'seance_length', 'master_id', 'deleted_at'
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function records()
    {
        return $this->belongsToMany(Record::class)->withPivot(['comission', 'profit']);
    }

    public function currency()
    {
        return $this->master->currency() ?? null;
    }
}
