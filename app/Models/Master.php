<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'origin_id', 'specialization', 'avatar', 'schedule_till', 'user_id', 'team_id', 'deleted_at', 'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function currency()
    {
        return $this->team->currency() ?? null;
    }
}
