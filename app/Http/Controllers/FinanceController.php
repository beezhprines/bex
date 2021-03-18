<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Manager;
use App\Models\Master;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function customOutcomes()
    {
        access(["can-owner", "can-host"]);
        $today = isodate();
        if(week()->start()<= $today && week()->end()>= $today ){
            $monthBudget = Budget::findByDateAndType($today, BudgetType::findByCode("custom:month:outcome"));
            $weekBudget = Budget::findByDateAndType($today, BudgetType::findByCode("custom:week:outcome"));

        }else{
            $monthBudget = Budget::findByDateAndType(week()->end(), BudgetType::findByCode("custom:month:outcome"));
            $weekBudget = Budget::findByDateAndType(week()->end(), BudgetType::findByCode("custom:week:outcome"));

        }


        return view("finances.custom-outcomes", [
            "monthBudget" => $monthBudget,
            "weekBudget" => $weekBudget
        ]);
    }

    public function updateCustomOutcomes(Request $request)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            "budget_id" => "required|exists:budgets,id",
            "custom-outcomes" => "nullable|array",
            "custom-outcomes.*.title" => "required|string",
            "custom-outcomes.*.amount" => "required|numeric",
        ]);

        $budget = Budget::find($data["budget_id"]);

        $today = isodate();
        if($budget->date==$today){

            foreach (daterange($today, week()->end(), true) as $date) {
                $date = date_format($date, config("app.iso_date"));
                $budgetToUpdate=Budget::findByDateAndType($date, BudgetType::findByCode("custom:month:outcome"));
                $budgetToUpdate->update([
                    "json" => json_encode($data["custom-outcomes"] ?? [])
                ]);
                Budget::solveCustomOutcomes($date);
            }
            note("info", "budget:custom-outcomes", "Обновлены расходы", Budget::class, $budget->id);
            return redirect("/finances/customOutcomes")->with(["success" => __("common.saved-success")]);

        }else{
            return redirect("/finances/customOutcomes?")->with('warning','Ошибка!  Невозможно изменить расходы за прошедшую неделю');
        }


    }

    public function statistics()
    {
        access(["can-owner", "can-host","can-recruiter"]);

        $masters = Master::all();

        $masterComissionBudgetType =  BudgetType::findByCode("master:comission:income");

        /* TOTAL STATISTICS */
        $startWeek = week()->start();
        $endWeek = week()->end();

        $budgetType = BudgetType::findByCode("marketer:team:instagram:outcome");
        $instagramOutcomes = Budget::getBetweenDatesAndType($startWeek, $endWeek, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            });

        $budgetType = BudgetType::findByCode("marketer:unexpected:outcome");
        $marketerOutcomes = Budget::getBetweenDatesAndType($startWeek, $endWeek, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            });

        $budgetType = BudgetType::findByCode("marketer:team:vk:outcome");
        $vkOutcomes = Budget::getBetweenDatesAndType($startWeek, $endWeek, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            });

        $budgetType = BudgetType::findByCode("manager:bonus:outcome");
        $managerBonuses = Budget::getBetweenDatesAndType($startWeek, $endWeek, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) * $budgetType->sign();

        $budgetType = BudgetType::findByCode("operator:profit:outcome");
        $operatorBonuses = Budget::getBetweenDatesAndType($startWeek, $endWeek, $budgetType)
            ->sum(function ($budget) {
                return $budget->amount ?? 0;
            }) * $budgetType->sign();

        $totalComission = Budget::getComission($startWeek, $endWeek);

        $total = [
            "totalComission" => $totalComission,
            "customOutcomes" => Budget::getCustomOutcomes($startWeek, $endWeek),
            "instagramOutcomes" => $instagramOutcomes,
            "vkOutcomes" => $vkOutcomes,
            "managerBonuses" => $managerBonuses,
            "operatorBonuses" => $operatorBonuses,
            "marketerOutcomes"=> $marketerOutcomes,
        ];

        $masterProfit = Budget::getMastersProfit($startWeek, $endWeek);

        $total["total"] = $masterProfit + $total["totalComission"];
        $total["profit"] = abs($total["totalComission"]) - abs($total["customOutcomes"]) - abs($total["instagramOutcomes"]) - abs($total["vkOutcomes"]) - abs($total["managerBonuses"]) - abs($total["operatorBonuses"]);

        /* END TOTAL STATISTICS */
        $user = User::find(Auth::id());

        if ($user->isRecruiter() ) {
            return view("recruiter.statistics", [
                "masters" => $masters,
                "masterComissionBudgetType" => $masterComissionBudgetType,
                "total" => $total
            ]);
        }else{
            return view("finances.statistics", [
                "masters" => $masters,
                "masterComissionBudgetType" => $masterComissionBudgetType,
                "total" => $total
            ]);
        }

    }

    public function payments()
    {
        access(["can-owner", "can-host"]);

        $managers = Manager::all();
        $operators = Operator::all();

        return view("finances.payments", [
            "managers" => $managers,
            "operators" => $operators,
        ]);
    }

    public function payManagerBudgets(Request $request, Manager $manager)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
            "action" => "required|in:0,1",
        ]);

        $manager->payBudgets($data["startDate"], $data["endDate"], $data["action"] == 1);

        return back()->with("success", __("common.saved-success"));
    }

    public function payOperatorBudgets(Request $request, Operator $operator)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            "startDate" => "required|date_format:Y-m-d",
            "endDate" => "required|date_format:Y-m-d",
            "action" => "required|in:0,1",
        ]);

        $operator->payBudgets($data["startDate"], $data["endDate"], $data["action"] == 1);

        return back()->with("success", __("common.saved-success"));
    }
}
