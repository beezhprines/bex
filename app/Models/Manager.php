<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'premium_rate', 'user_id', 'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function budgets()
    {
        return $this->belongsToMany(Budget::class);
    }

    public static function getMilestoneBonus(float $totalComission)
    {
        return collect(json_decode(Configuration::findByCode("manager:milestones")->value, true))
            ->filter(function ($milestone) use ($totalComission) {
                return $totalComission >= $milestone['profit'];
            })
            ->last()['bonus'] ?? 0;
    }

    public static function solveBonus(float $comission, float $premium_rate)
    {
        // get base manager comission coefficient
        $comissionCoef = floatval(Configuration::findByCode("manager:profit")->value);

        // get milestones bonus
        $bonus = self::getMilestoneBonus($comission);

        // solve
        return (($comissionCoef * $comission) + floatval($bonus)) * floatval($premium_rate);
    }
}
