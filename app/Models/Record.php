<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'origin_id', 'started_at', 'duration', 'comment', 'attendance', 'master_id'
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot(['comission', 'profit']);
    }
}
