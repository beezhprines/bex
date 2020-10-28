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

        $instagram = collect(json_decode(Budget::findByDateAndType(week()->last(), $budgetTypeInstagram)->json, true));
        $vk = collect(json_decode(Budget::findByDateAndType(week()->last(), $budgetTypeVK)->json, true));
        $teams = Team::all();

        $currencyRates = [];
        foreach (Currency::all() as $currency) {
            $currencyRates[$currency->code] = CurrencyRate::findByCurrencyAndDate($currency, week()->last());
        }

        return view("marketers.analytics", [
            "teams" => $teams,
            "instagram" => $instagram,
            "vk" => $vk,
            "currencyRates" => collect($currencyRates)
        ]);
    }

    public function saveTeamOutcomes(Request $request)
    {
        access(["can-marketer"]);

        $data = $request->validate([
            "date" => "required|date",
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
            $budget = Budget::findByDateAndType($data["date"], $budgetType);

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
                    "date" => $data["date"],
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
            return route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }
}
