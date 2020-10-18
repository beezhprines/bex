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

    public static function seedOutcomes(string $startDate, string $endDate)
    {
        $budgetTypeInstagram = BudgetType::findByCode('marketer:team:instagram:outcome');
        $budgetTypeVK = BudgetType::findByCode('marketer:team:vk:outcome');
        $dates = daterange($startDate, $endDate, true);

        $teams = self::all();

        collect($dates)->each(function ($date) use ($teams, $budgetTypeInstagram, $budgetTypeVK) {
            Budget::create([
                'date' => $date,
                'json' => $teams->map(function ($team) {
                    return [
                        'team_id' => $team->id,
                        'amount' => 0
                    ];
                }),
                'budget_type_id' => $budgetTypeInstagram->id,
            ]);

            Budget::create([
                'date' => $date,
                'json' => $teams->map(function ($team) {
                    return [
                        'team_id' => $team->id,
                        'amount' => 0
                    ];
                }),
                'budget_type_id' => $budgetTypeVK->id,
            ]);
        });

        note("info", "budget:seed", "Созданы затраты на команду с {$startDate} по {$endDate}", Budget::class);
    }
}
