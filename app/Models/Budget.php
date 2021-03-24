<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        "date", "amount", "json", "paid", "budget_type_id"
    ];

    public function budgetType()
    {
        return $this->belongsTo(BudgetType::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function managers()
    {
        return $this->belongsToMany(Manager::class)->withTimestamps();
    }

    public function masters()
    {
        return $this->belongsToMany(Master::class)->withTimestamps();
    }

    public function cosmetologists()
    {
        return $this->belongsToMany(Cosmetologist::class)->withTimestamps();
    }

    public function operators()
    {
        return $this->belongsToMany(Operator::class)->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function master()
    {
        return $this->masters->first();
    }

    public function cosmetologist()
    {
        return $this->cosmetologists->first();
    }

    public static function seedCustomOutcomes(string $startDate, string $endDate)
    {
        $budgetTypes = [
            BudgetType::findByCode("custom:month:outcome"),
            BudgetType::findByCode("custom:week:outcome"),
            BudgetType::findByCode("marketer:unexpected:outcome")
        ];

        foreach ($budgetTypes as $budgetType) {

            foreach (daterange($startDate, $endDate, true) as $date) {
                $date = date_format($date, config("app.iso_date"));
                $yesterday = date(config("app.iso_date"), strtotime($date."-1 days"));

                $json = Budget::findByDateAndType($yesterday, $budgetType)->json;

                $budget = $budgetType->budgets()->firstWhere("date", $date);

                if (!empty($budget)) continue;

                Budget::create([
                    "date" => $date,
                    "json" => $json,
                    "budget_type_id" => $budgetType->id
                ]);
            }
        }
    }

    public static function solveComission(string $date)
    {
        $budgetType = BudgetType::findByCode("total:comission:income");

        // get master records for date
        $amount = Record::solveComission(Record::get($date, $date)) * $budgetType->sign();

        // get cosmetologists comission for date
        $amount += self::getCosmetologistComission($date, $date);

        // get unexpected master comissions for date
        $amount += self::getUnexpectedMasterComission($date, $date);

        $budget = self::findByDateAndType($date, $budgetType);

        if (empty($budget)) {
            $budget = self::create([
                "amount" => $amount,
                "date" => $date,
                "budget_type_id" => $budgetType->id
            ]);
        } else {
            $budget->update([
                "amount" => $amount
            ]);
        }

        note("info", "budget:solve:total:comission", "Подсчитана общая комиссия на дату {$date}", self::class, $budget->id);
    }

    public static function solveMastersComission(string $date)
    {
        $budgetType =  BudgetType::findByCode("master:comission:income");

        $masters = Master::all();

        foreach ($masters as $master) {
            $amount = $master->solveComission($date, $date) * $budgetType->sign();

            $budget = $master->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    "amount" => $amount,
                    "date" => $date,
                    "budget_type_id" => $budgetType->id,
                ]);

                $budget->masters()->attach($master);
            } else {
                $budget->update([
                    "amount" => $amount
                ]);
            }
        }

        note("info", "budget:solve:master:comission", "Подсчитана комиссия мастеров на дату {$date}", self::class);
    }

    public static function solveMastersProfit(string $date)
    {
        $budgetType = BudgetType::findByCode("master:profit:outcome");

        $masters = Master::all();

        foreach ($masters as $master) {
            $amount = $master->solveProfit($date, $date) * $budgetType->sign();

            $budget = $master->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    "amount" => $amount,
                    "date" => $date,
                    "budget_type_id" => $budgetType->id,
                ]);

                $budget->masters()->attach($master);
            } else {
                $budget->update([
                    "amount" => $amount
                ]);
            }
        }

        note("info", "budget:solve:master:profit", "Подсчитана выручка мастеров на дату {$date}", Budget::class);
    }

    public static function getCosmetologistComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("cosmetologist:comission:income");

        $amount = self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) ?? 0;

        return $amount;
    }

    public static function solveCosmetologistComission(string $date, float $amount, Cosmetologist $cosmetologist)
    {
        $budgetType = BudgetType::findByCode("cosmetologist:comission:income");

        $budget = $cosmetologist->getBudget($date, $budgetType->id);

        if (empty($budget)) {
            $budget = self::create([
                "amount" => $amount,
                "date" => $date,
                "budget_type_id" => $budgetType->id,
            ]);

            $budget->cosmetologists()->attach($cosmetologist);
        } else {
            $budget->update([
                "amount" => $amount
            ]);
        }
    }

    public static function getUnexpectedMasterComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("master:unexpected:income");

        $amount = self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) ?? 0;

        return $amount;
    }

    public static function solveUnexpectedMasterComission(string $date, float $amount, Master $master)
    {
        $budgetType = BudgetType::findByCode("master:unexpected:income");

        $budget = $master->getBudget($date, $budgetType->id);

        if (empty($budget)) {
            $budget = Budget::create([
                "amount" => $amount,
                "date" => $date,
                "budget_type_id" => $budgetType->id
            ]);

            $budget->masters()->attach($master);
        } else {
            $budget->update([
                "amount" => $amount
            ]);
        }
    }

    public static function solveCustomOutcomes(string $date)
    {
        $types = [
            ["budgetType" => BudgetType::findByCode("custom:month:outcome"), "days" => date("t", strtotime($date))],
            ["budgetType" => BudgetType::findByCode("custom:week:outcome"), "days" => 7],
            ["budgetType" => BudgetType::findByCode("marketer:unexpected:outcome"), "days" => 7]

        ];

        foreach ($types as $type) {

            $budget = self::findByDateAndType($date, $type["budgetType"]);

            $budgetTypeCode = $type["budgetType"]->code;

            if (empty($budget)) throw new Exception("Бюджет на дату {$date} и типом {$budgetTypeCode} не найден");

            if (empty($budget->json)) continue;

            $amount = collect(json_decode($budget->json, true))->sum(function ($outcome) {
                return floatval($outcome["amount"] ?? 0);
            }) / $type["days"] * $type["budgetType"]->sign();

            $budget->update([
                "amount" => $amount
            ]);

            note("info", "budget:solve:{$budgetTypeCode}", "Подсчитаны доп затраты на дату {$date} для {$budgetTypeCode}", self::class, $budget->id);
        }
    }

    public static function getCustomOutcomes(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("custom:month:outcome");

        $amount = self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) ?? 0;

        $budgetType = BudgetType::findByCode("custom:week:outcome");

        $amount += self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) ?? 0;


        return $amount;
    }

    public static function solveManagersBonus(string $date)
    {
        $budgetType =  BudgetType::findByCode("manager:bonus:outcome");

        $comission = self::getComission($date, $date);

        $managers = Manager::all();

        foreach ($managers as $manager) {
            $amount = Manager::solveBonus($comission, $manager->premium_rate,$date) * $budgetType->sign();

            $budget = $manager->budgets->firstWhere("date", $date);

            if (empty($budget)) {
                $budget = self::create([
                    "amount" => $amount,
                    "date" => $date,
                    "budget_type_id" => $budgetType->id
                ]);

                $budget->managers()->attach($manager);
            } else {
                $budget->update([
                    "amount" => $amount
                ]);
            }
        }

        note("info", "budget:solve:manager:bonus", "Подсчитаны бонусы менеджеров на дату {$date}", self::class);
    }

    public static function solveOperatorsProfit(string $date)
    {
        $budgetType =  BudgetType::findByCode("operator:profit:outcome");

        $operators = Operator::all();

        foreach ($operators as $operator) {
            $amount = $operator->solveProfit($operator->solveComission($date, $date)) * $budgetType->sign();

            $budget = $operator->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    "amount" => $amount,
                    "date" => $date,
                    "budget_type_id" => $budgetType->id
                ]);

                $budget->operators()->attach($operator);
            } else {
                $budget->update([
                    "amount" => $amount
                ]);
            }
        }

        note("info", "budget:solve:operator:profit", "Подсчитаны бонусы операторов на дату {$date}", self::class);
    }

    public static function getComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("total:comission:income");

        return self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount;
            });
    }

    public static function getMastersProfit(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode("master:profit:outcome");

        $amount = round(
            self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
                ->sum(function ($budget) {
                    return $budget->amount ?? 0;
                })
        );

        return $amount == 0 ? 0 : $amount *  $budgetType->sign();
    }

    public static function getBetweenDatesAndType(string $startDate, string $endDate, BudgetType $budgetType)
    {
        return self::whereBetween(DB::raw("DATE(date)"), array($startDate, $endDate))
            ->where("budget_type_id", $budgetType->id)
            ->get();
    }

    public static function findByDateAndType(string $date, BudgetType $budgetType)
    {
        return self::firstWhere([
            "budget_type_id" => $budgetType->id,
            "date" => $date
        ]);
    }

    public static function getByDateAndType(string $date, BudgetType $budgetType)
    {
        return self::where([
            "budget_type_id" => $budgetType->id,
            "date" => $date
        ])->get();
    }

    public static function getComissionsPerWeek()
    {
        $comissions = [];
        $budgetType = BudgetType::findByCode("total:comission:income");

        $weeks = intval(Configuration::findByCode("manager:comission:weeks")->value);

        for ($i = 0; $i < $weeks; $i++) {
            $diff = 7 * $i;
            $weekday = date(config("app.iso_date"), strtotime("-{$diff} day"));
            $weekStart = week()->monday($weekday);
            $weekEnd = week()->sunday($weekday);
            $comissions[$weekStart] = [];

            $prev = 0;
            foreach (daterange($weekStart, $weekEnd, true) as $date) {
                $comissions[$weekStart][date_format($date, "D")] = floatval(
                    self::findByDateAndType(
                        date_format($date, config("app.iso_date")),
                        $budgetType
                    )->amount ?? 0
                ) / 1000 + $prev;

                $prev = $comissions[$weekStart][date_format($date, "D")];
            }
            $comissions[$weekStart]["total"] = $prev;
        }

        return $comissions;
    }

    public static function solveMastersPenalty(string $date)
    {
        $startDate = week()->monday($date);
        $endDate = week()->sunday($date);
        $budgetType = BudgetType::findByCode("master:penalty:income");
        $masterComissionBudgetType = BudgetType::findByCode("master:comission:income");
        $masters = Master::all();

        foreach ($masters as $master) {
            $masterComissionBudget = $master->getBudget($endDate, $masterComissionBudgetType->id);
            if (empty($masterComissionBudget) || $masterComissionBudget->invoices->count() > 0) continue;

            $weekComission = $master->getComission($startDate, $endDate);
            $amount = $master->solvePenalty($date, $weekComission);
            if (is_null($amount) || $amount == 0) {
                $amount = 0;
            }

            $budget = $master->getBudget($startDate, $budgetType->id);

            if (empty($budget)) {
                echo "\n"." create ";

                $budget = self::create([
                    "amount" => $amount,
                    "date" => $startDate,
                    "budget_type_id" => $budgetType->id
                ]);

                $budget->masters()->attach($master);
            } else {
                echo "\n"." update ";
                $budget->update([
                    "amount" => $amount
                ]);
            }
        }

        note("info", "budget:solve:master:penalty", "Подсчитаны пени мастеров на дату {$date}", self::class);
    }
}
