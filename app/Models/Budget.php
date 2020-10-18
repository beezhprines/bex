<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        $budget = self::firstWhere([
            'budget_type_id' => $budgetType->id,
            'date' => $date
        ]);

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
}
