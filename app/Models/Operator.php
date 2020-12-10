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

    public function updateWithRelations(array $data)
    {
        $userData = [
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
        ];

        if (!empty($data["user"]["password"])) {
            $userData["password"] = bcrypt(trim($data["user"]["password"]));
            $userData["open_password"] = $data["user"]["password"];
        }

        $user = $this->user->update($userData);

        $this->update(["name" => $data["name"]]);

        note("info", "operator:update", "Обновлены данные оператора {$this->name}", self::class, $this->id);

        $this->fresh();

        return $this;
    }

    public static function createWithRelations(array $data)
    {
        $role = Role::findByCode("operator");

        $user = User::create([
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
            "password" => $data["user"]["password"],
            "open_password" => $data["user"]["password"],
            "role_id" => $role->id
        ]);

        $operator = self::create([
            "user_id" => $user->id,
            "name" => $data["name"],
        ]);

        note("info", "operator:create", "Создан новый оператор {$operator->name}", self::class, $operator->id);

        return $operator;
    }

    public function isBonusPaid(string $startDate, string $endDate)
    {
        return $this->budgets->whereBetween("date", [$startDate, $endDate])
            ->where("paid", false)->count() == 0;
    }

    public function payBudgets(string $startDate, string $endDate, bool $paid)
    {
        $budgets = $this->budgets()->whereBetween("date", [$startDate, $endDate])
            ->where("paid", !$paid)->get();

        foreach ($budgets as $budget) {
            $budget->update([
                "paid" => $paid
            ]);
        };
    }
}
