<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        "user_id", "name"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function budgets()
    {
        return $this->belongsToMany(Budget::class)->withTimestamps();
    }

    public function solveComission(string $startDate, string $endDate)
    {
        return $this->teams->sum(function ($team) use ($startDate, $endDate) {
            return $team->solveComission($startDate, $endDate);
        });
    }

    public function getPoints(float $profit)
    {
        return round($profit / ($this->profitCoef() * $this->pointValue()));
    }

    private function profitCoef()
    {
        return floatval(Configuration::findByCode("operator:profit")->value);
    }

    private function pointValue()
    {
        return floatval(Configuration::findByCode("operator:point")->value);
    }

    public function solveProfit(float $comission)
    {
        return $comission * $this->profitCoef();
    }

    public function solvePointsPerService(Service $service, string $startDate, string $endDate)
    {
        $comission = $service->solveComission($startDate, $endDate);

        return round($comission / $this->pointValue());
    }

    public function solvePointsPerTeam(Team $team, string $startDate, string $endDate)
    {
        $comission = $team->solveComission($startDate, $endDate);

        return round($comission / $this->pointValue());
    }

    public function solvePointsPerMaster(Master $master, string $startDate, string $endDate)
    {
        $comission = $master->getComission($startDate, $endDate) * floatval($master->team->premium_rate);

        return round($comission / $this->pointValue());
    }

    public function getProfit(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("operator:profit:outcome");

        $amount = round(
            $this->budgets
                ->whereBetween("date", [$startDate, $endDate])
                ->where("budget_type_id", $budgetType->id)
                ->sum(function ($budget) {
                    return $budget->amount;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public function getBudget(string $date, int $budgetTypeId)
    {
        return $this->budgets
            ->where("date", $date)
            ->where("budget_type_id", $budgetTypeId)
            ->first();
    }
}
