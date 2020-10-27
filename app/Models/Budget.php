<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'date', 'amount', 'json', 'budget_type_id'
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

    public function operators()
    {
        return $this->belongsToMany(Operator::class)->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function seedCustomOutcomes(string $startDate, string $endDate)
    {
        $budgetTypes = [
            BudgetType::findByCode('custom:month:outcome'),
            BudgetType::findByCode('custom:week:outcome')
        ];

        foreach ($budgetTypes as $budgetType) {
            $json = $budgetType->budgets()->whereNotNull('json')->orderBy('date')->first()->json ?? null;

            foreach (daterange($startDate, $endDate, true) as $date) {
                $date = date_format($date, config('app.iso_date'));
                $budget = $budgetType->budgets()->firstWhere('date', $date);

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
        $budgetType = BudgetType::findByCode('total:comission:income');

        $amount = Record::solveComission(Record::get($date, $date)) * $budgetType->sign();

        $budget = self::findByDateAndType($date, $budgetType);

        if (empty($budget)) {
            $budget = self::create([
                'amount' => $amount,
                'date' => $date,
                'budget_type_id' => $budgetType->id
            ]);
        } else {
            $budget->update([
                'amount' => $amount
            ]);
        }

        note("info", "budget:solve:total:comission", "Подсчитана общая комиссия на дату {$date}", self::class, $budget->id);
    }

    public static function solveMastersComission(string $date)
    {
        $budgetType =  BudgetType::findByCode('master:comission:income');

        $masters = Master::all();

        foreach ($masters as $master) {
            $amount = $master->solveComission($date, $date) * $budgetType->sign();

            $budget = $master->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    'amount' => $amount,
                    'date' => $date,
                    'budget_type_id' => $budgetType->id,
                ]);

                $budget->masters()->attach($master);
            } else {
                $budget->update([
                    'amount' => $amount
                ]);
            }
        }

        note("info", "budget:solve:master:comission", "Подсчитана комиссия мастеров на дату {$date}", self::class);
    }

    public static function solveMastersProfit(string $date)
    {
        $budgetType = BudgetType::findByCode('master:profit:outcome');

        $masters = Master::all();

        foreach ($masters as $master) {
            $amount = $master->solveProfit($date, $date) * $budgetType->sign();

            $budget = $master->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    'amount' => $amount,
                    'date' => $date,
                    'budget_type_id' => $budgetType->id,
                ]);

                $budget->masters()->attach($master);
            } else {
                $budget->update([
                    'amount' => $amount
                ]);
            }
        }

        note("info", "budget:solve:master:profit", "Подсчитана выручка мастеров на дату {$date}", Budget::class);
    }

    public static function solveCustomOutcomes(string $date)
    {
        $types = [
            ["budgetType" => BudgetType::findByCode('custom:month:outcome'), "days" => date("t", strtotime($date))],
            ["budgetType" => BudgetType::findByCode('custom:week:outcome'), "days" => 7]
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
                'amount' => $amount
            ]);

            note("info", "budget:solve:{$budgetTypeCode}", "Подсчитаны доп затраты на дату {$date} для {$budgetTypeCode}", self::class, $budget->id);
        }
    }

    public static function solveManagersBonus(string $date)
    {
        $budgetType =  BudgetType::findByCode('manager:bonus:outcome');

        $comission = self::getComission($date, $date);

        $managers = Manager::all();

        foreach ($managers as $manager) {
            $amount = Manager::solveBonus($comission, $manager->premium_rate) * $budgetType->sign();

            $budget = $manager->budgets->firstWhere('date', $date);

            if (empty($budget)) {
                $budget = self::create([
                    'amount' => $amount,
                    'date' => $date,
                    'budget_type_id' => $budgetType->id
                ]);

                $budget->managers()->attach($manager);
            } else {
                $budget->update([
                    'amount' => $amount
                ]);
            }
        }

        note("info", "budget:solve:manager:bonus", "Подсчитаны бонусы менеджеров на дату {$date}", self::class);
    }

    public static function solveOperatorsProfit(string $date)
    {
        $budgetType =  BudgetType::findByCode('operator:profit:outcome');

        $operators = Operator::all();

        foreach ($operators as $operator) {
            $amount = $operator->solveProfit($operator->solveComission($date, $date)) * $budgetType->sign();

            $budget = $operator->getBudget($date, $budgetType->id);

            if (empty($budget)) {
                $budget = self::create([
                    'amount' => $amount,
                    'date' => $date,
                    'budget_type_id' => $budgetType->id
                ]);

                $budget->operators()->attach($operator);
            } else {
                $budget->update([
                    'amount' => $amount
                ]);
            }
        }

        note("info", "budget:solve:operator:profit", "Подсчитаны бонусы операторов на дату {$date}", seld::class);
    }

    public static function getComission(string $startDate, string $endDate)
    {
        $budgetType = BudgetType::findByCode('total:comission:income');

        return self::getBetweenDatesAndType($startDate, $endDate, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount;
            });
    }

    public static function getBetweenDatesAndType(string $startDate, string $endDate, BudgetType $budgetType)
    {
        return self::whereBetween(DB::raw('DATE(date)'), array($startDate, $endDate))
            ->where('budget_type_id', $budgetType->id)
            ->get();
    }

    public static function findByDateAndType(string $date, BudgetType $budgetType)
    {
        return self::firstWhere([
            'budget_type_id' => $budgetType->id,
            'date' => $date
        ]);
    }
}
