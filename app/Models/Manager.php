<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        "premium_rate", "user_id", "name"
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
                return $totalComission >= $milestone["profit"];
            })
            ->last()["bonus"] ?? 0;
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

    public function getBonus(string $startDate, string $endDate)
    {
        return $this->budgets->whereBetween("date", [$startDate, $endDate])
            ->sum(function ($budget) {
                return $budget->amount;
            }) * -1;
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

        if (!empty($data["premium_rate"])) {
            $this->update([
                "premium_rate" => floatval($data["premium_rate"]) / 100,
                "name" => $data["name"],
            ]);
        }

        note("info", "manager:update", "Обновлены данные менеджера {$this->name}", self::class, $this->id);

        return $this;
    }

    public static function createWithRelations(array $data)
    {
        $role =  Role::findByCode("manager");

        $user = User::create([
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
            "password" => $data["user"]["password"],
            "open_password" => $data["user"]["password"],
            "role_id" => $role->id
        ]);

        $manager = self::create([
            "user_id" => $user->id,
            "name" => $data["name"],
            "premium_rate" => floatval($data["premium_rate"]) / 100
        ]);

        note("info", "manager:create", "Создан новый менеджер {$manager->name}", self::class, $manager->id);

        return $manager;
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
