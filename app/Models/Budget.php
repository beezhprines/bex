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
        return $this->belongsToMany(Manager::class, 'budget_manager');
    }

    public function masters()
    {
        return $this->belongsToMany(Master::class, 'budget_master');
    }

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'budget_operator');
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
}
