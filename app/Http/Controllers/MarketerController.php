<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Configuration;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Marketer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $marketers = Marketer::all();

        return view("marketers.index", [
            'marketers' => $marketers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'name' => 'required|string|min:3',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string'
        ]);

        $marketer = Marketer::createWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function show(Marketer $marketer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function edit(Marketer $marketer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marketer $marketer)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'name' => 'required|string|min:3',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string'
        ]);

        $marketer = $marketer->updateWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketer $marketer)
    {
        access(["can-owner", "can-host"]);

        $marketerId = $marketer->id;
        $marketerName = $marketer->name;

        $marketer->user->delete();
        $marketer->delete();

        note("info", "marketer:delete", "Удален маркетолог {$marketerName}", Marketer::class, $marketerId);

        return back()->with(['success' => __('common.deleted-success')]);
    }


    public function analytics()
    {
        access(["can-marketer"]);

        $budgetTypeInstagram = BudgetType::findByCode("marketer:team:instagram:outcome");
        $budgetTypeVK = BudgetType::findByCode("marketer:team:vk:outcome");
        $budgetMarketerUnOut = Budget::findByDateAndType(week()->start(), BudgetType::findByCode("marketer:unexpected:outcome"));

        $instagram = collect(json_decode(Budget::findByDateAndType(week()->end(), $budgetTypeInstagram)->json, true));
        $vk = collect(json_decode(Budget::findByDateAndType(week()->end(), $budgetTypeVK)->json, true));
        $teams = Team::all();

        $nextMonday = week()->monday(week()->next(week()->last()));
        $currencyRateDate = (isodate() < $nextMonday) ? week()->last() : $nextMonday;
        $currencyRates = [];
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            $currencyRates[$currency->code] = CurrencyRate::findByCurrencyAndDate($currency, $currencyRateDate);
        }

        return view("marketers.analytics", [
            "teams" => $teams,
            "instagram" => $instagram,
            "vk" => $vk,
            "currencyRates" => collect($currencyRates),
            "currencies" => $currencies,
            "currencyRateDate" => $currencyRateDate,
            "budgetMarketerUnOut" => $budgetMarketerUnOut
        ]);
    }
    public function updateMarketerCustomOutcomes(Request $request)
    {
        access(["can-marketer"]);

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

    public function saveTeamOutcomes(Request $request)
    {
        access(["can-marketer"]);

        $data = $request->validate([
            "teams" => "required|array",
            "teams.*.id" => "required|exists:teams,id",
            "teams.*.instagram" => "nullable|numeric",
            "teams.*.vk" => "nullable|numeric",
        ]);

        $budgetTypes = [
            "instagram" => BudgetType::findByCode("marketer:team:instagram:outcome"),
            "vk" => BudgetType::findByCode("marketer:team:vk:outcome")
        ];

        $teams = collect($data["teams"]);

        foreach ($budgetTypes as $key => $budgetType) {
            $budget = Budget::findByDateAndType(week()->end(), $budgetType);

            $json = $teams->map(function ($team) use ($key) {
                return [
                    "amount" => floatval($team[$key]),
                    "team_id" => intval($team["id"])
                ];
            });

            $amount = $json->sum(function ($team) {
                return $team["amount"];
            }) * $budgetType->sign();

            if (empty($budget)) {
                $budget = Budget::create([
                    "amount" => $amount,
                    "json" => json_encode(array_values($json->toArray())),
                    "date" => week()->end(),
                    "budget_type_id" => $budgetType->id
                ]);
            } else {
                $budget->update([
                    "amount" => $amount,
                    "json" => json_encode(array_values($json->toArray())),
                ]);
            }
        }

        return back()->with(["success" => __("common.saved-success")]);
    }

    public function diagrams()
    {
        access(["can-marketer"]);

        $milestones = collect(json_decode(Configuration::findByCode("manager:milestones")->value, true))
            ->map(function ($milestone) {
                $milestone["bonus"] = null;
                return $milestone;
            });

        $totalComission =  Budget::getComission(week()->start(), week()->end());

        return view("marketers.diagrams", [
            "milestones" => $milestones,
            "totalComission" => $totalComission,
        ]);
    }

    public function auth(Marketer $marketer)
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($marketer->user);
            return redirect()->route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }
}
