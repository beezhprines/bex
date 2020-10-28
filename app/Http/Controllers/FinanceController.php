<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Master;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function customOutcomes()
    {
        $monthBudget = Budget::findByDateAndType(month()->start(week()->end()), BudgetType::findByCode("custom:month:outcome"));

        $weekBudget = Budget::findByDateAndType(week()->start(), BudgetType::findByCode("custom:week:outcome"));

        return view("finances.custom-outcomes", [
            "monthBudget" => $monthBudget,
            "weekBudget" => $weekBudget
        ]);
    }

    public function updateCustomOutcomes(Request $request)
    {
        $data = $request->validate([
            "budget_id" => "required|exists:budgets,id",
            "custom-outcomes" => "nullable|array",
            "custom-outcomes.*.title" => "required|string",
            "custom-outcomes.*.amount" => "required|numeric",
        ]);

        $budget = Budget::find($data["budget_id"]);

        $budget->update([
            "json" => json_encode($data["custom-outcomes"] ?? [])
        ]);

        note("info", "budget:custom-outcomes", "Обновлены расходы", Budget::class, $budget->id);

        return redirect()->back()->with(["success" => __("common.saved-success")]);
    }

    public function statistics()
    {
        $masters = Master::all();

        $budgetType =  BudgetType::findByCode("master:comission:income");

        return view("finances.statistics", [
            "masters" => $masters,
            "budgetType" => $budgetType
        ]);
    }
}
