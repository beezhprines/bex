<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Configuration;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Manager;
use App\Models\Master;
use App\Models\Operator;
use App\Models\Team;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manager $manager)
    {
        //
    }

    public function weekplan(Request $request)
    {
        $milestones = collect(json_decode(Configuration::findByCode("manager:milestones")->value, true));
        $comission = Budget::getComission(week()->start(), week()->end());
        $managerBonusRate = floatval(Configuration::findByCode("manager:profit")->value);
        $milestoneBonus = Manager::getMilestoneBonus($comission);
        $manager = Manager::first(); // todo Auth::user()->manager;
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
        return view("managers.comissions", [
            "masters" => $this->masterRepo->all()
        ]);
    }

    public function monitoring()
    {
        $comissions = $this->budgetRepo->getComissionsPerWeek();
        $masters = $this->invoiceRepo->getMastersNotLoadedInvoiceForWeek(week()->end());

        return view("managers.monitoring", [
            "comissions" => $comissions,
            "masters" => $masters
        ]);
    }

    public function currencyRates()
    {
        $currencyRUB = BaseRepo::instance(Currency::class)->findByCode("RUB");
        $currencyRates = $currencyRUB->rates()->orderByDesc("date")->paginate();

        return view("managers.currency-rates", [
            "currencyRates" => $currencyRates
        ]);
    }
}
