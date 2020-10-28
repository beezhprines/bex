<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Configuration;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Manager;
use App\Models\Master;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $managers = Manager::all();

        return view("managers.index", [
            'managers' => $managers
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
        $data = $request->validate([
            'name' => 'required|string|min:3',
            'premium_rate' => 'required|numeric',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string'
        ]);

        $manager = Manager::createWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function show(Manager $manager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit(Manager $manager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Manager $manager)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3',
            'premium_rate' => 'required|numeric',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string'
        ]);

        $manager = $manager->updateWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manager $manager)
    {
        $managerId = $manager->id;
        $managerName = $manager->name;

        $manager->user->delete();
        $manager->delete();

        note("info", "manager:delete", "Удален менеджер {$managerName}", Manager::class, $managerId);

        return back()->with(['success' => __('common.deleted-success')]);
    }

    public function weekplan(Request $request)
    {
        $milestones = collect(json_decode(Configuration::findByCode("manager:milestones")->value, true));
        $comission = Budget::getComission(week()->start(), week()->end());
        $managerBonusRate = floatval(Configuration::findByCode("manager:profit")->value);
        $milestoneBonus = Manager::getMilestoneBonus($comission);
        $manager = Auth::user()->manager;
        $masters = Master::all();

        return view("managers.weekplan", [
            "milestones" => $milestones,
            "comission" => $comission,
            "managerBonusRate" => $managerBonusRate,
            "milestoneBonus" => $milestoneBonus,
            "masters" => $masters,
            "manager" => $manager
        ]);
    }

    public function statistics(Request $request)
    {
        $teams = Team::all();
        $team = $request->has('team') ? $teams->find($request->team) : $teams->first();

        $contactTypes = ContactType::all();
        $contacts = collect();
        foreach ($contactTypes as $contactType) {
            $contacts = $contacts->merge(Contact::getByDatesTypeTeam(week()->start(), week()->end(), $team, $contactType));
        }

        return view("managers.statistics", [
            'team' => $team,
            'teams' => $teams,
            'contactTypes' => $contactTypes,
            'contacts' => $contacts->groupBy("date"),
        ]);
    }

    public function diagrams()
    {
        return view("managers.diagrams");
    }

    public function comissions(Request $request)
    {
        $masters = Master::all();
        return view("managers.comissions", [
            "masters" => $masters
        ]);
    }

    public function monitoring()
    {
        $comissions = Budget::getComissionsPerWeek();
        $masters = Invoice::getMastersNotLoadedInvoiceForWeek(week()->end());

        return view("managers.monitoring", [
            "comissions" => $comissions,
            "masters" => $masters
        ]);
    }

    public function currencyRates()
    {
        $currencyCount = Currency::count();
        $currencyRatesPaginator = CurrencyRate::orderByDesc("date")->paginate($currencyCount * 15);
        $currencyRatesGrouped = collect($currencyRatesPaginator->items())->groupBy("date");

        return view("managers.currency-rates", [
            "currencyRatesPaginator" => $currencyRatesPaginator,
            "currencyRatesGrouped" => $currencyRatesGrouped
        ]);
    }

    public function auth(Manager $manager)
    {
        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($manager->user);
            return route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }
}
