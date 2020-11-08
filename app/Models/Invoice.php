<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'file', 'budget_id', 'confirmed_date'
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public static function getMastersNotLoadedInvoiceForWeek(string $sundayDate)
    {
        $budgetType = BudgetType::findByCode('master:comission:income');

        $budgets = Budget::getByDateAndType($sundayDate, $budgetType);

        return $budgets->filter(function ($budget) {
            return count($budget->invoices) == 0;
        })->map(function ($budget) {
            return $budget->master();
        });
    }
}
