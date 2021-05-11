<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Configuration;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Cosmetologist;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Manager;
use App\Models\Master;
use App\Models\Operator;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
        access(["can-owner", "can-host"]);

        $managersTemp = Manager::all();
        $managers = [];
        foreach ($managersTemp as $m){
            $u = User::find($m->user_id);
            if($u->role->code == "manager"){
                $managers[]=$m;
            }
        }


        return view("managers.index", [
            "managers" => $managers
        ]);
    }
    public function recruiter()
    {
        access(["can-owner", "can-host"]);

        $managersTemp = Manager::all();
        $managers = [];
        foreach ($managersTemp as $m){
            $u = User::find($m->user_id);
            if($u->role->code == "recruiter"){
                $managers[]=$m;
            }
        }

        return view("managers.index", [
            "managers" => $managers
        ]);
    }
    public function chief_operator()
    {
        access(["can-owner", "can-host"]);

        $managersTemp = Manager::all();
        $managers = [];
        foreach ($managersTemp as $m){
            $u = User::find($m->user_id);
            if($u->role->code == "chief-operator"){
                $managers[]=$m;
            }
        }

        return view("managers.index", [
            "managers" => $managers
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
            "name" => "required|string|min:3",
            "premium_rate" => "required|numeric",
            "user" => "required|array",
            "user.account" => "required|string|min:3",
            "user.password" => "nullable|string|min:3",
            "user.email" => "nullable|email",
            "user.phone" => "nullable|string"
        ]);

        $manager = Manager::createWithRelations($data);

        return back()->with(["success" => __("common.saved-success")]);
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
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            "name" => "required|string|min:3",
            "premium_rate" => "required|numeric",
            "user" => "required|array",
            "user.account" => "required|string|min:3",
            "user.password" => "nullable|string|min:3",
            "user.email" => "nullable|email",
            "user.phone" => "nullable|string"
        ]);

        $manager = $manager->updateWithRelations($data);

        return back()->with(["success" => __("common.saved-success")]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Manager  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manager $manager)
    {
        access(["can-owner", "can-host"]);

        $managerId = $manager->id;
        $managerName = $manager->name;

        $manager->user->delete();
        $manager->delete();

        note("info", "manager:delete", "Удален менеджер {$managerName}", Manager::class, $managerId);

        return back()->with(["success" => __("common.deleted-success")]);
    }

    public function weekplan(Request $request)
    {
        access(["can-manager", "can-owner", "can-host", "can-recruiter"]);

        $milestones = collect(json_decode(Configuration::findByCode("manager:milestones")->value, true));
        $comission = Budget::getComission(week()->start(), week()->end()) + Budget::getUnexpectedMasterComission(week()->start(), week()->end());
        $managerBonusRate = floatval(Configuration::findByCode("manager:profit")->value);
        $milestoneBonus = Manager::getMilestoneBonus($comission);
        $masters = Master::all();
        $operators = Operator::all();
        $managers = Manager::all();
        $user = User::find(Auth::id());
        $teamWithOutOper = array();
        $teamWithOutTown = array();
        $mastersWithOutTeam = array();

        $teams = Team::all();
        foreach ($masters as $m){
            $tempTeam = Team::find($m->team_id);
            if($tempTeam==null){
                $mastersWithOutTeam[]=$m;
            }
        }

        foreach ($teams as $t){
            if($t->id==1){
                var_dump($t->city_id);
            }
            if(!$t->operator_id){
                $teamWithOutOper[]=$t;
            }
            if(!$t->city_id){
                $teamWithOutTown[]=$t;

            }

        }
        if ($user->isRecruiter()) {
            return view("recruiter.weekplan", [
                "milestones" => $milestones,
                "comission" => $comission,
                "managerBonusRate" => $managerBonusRate,
                "milestoneBonus" => $milestoneBonus,
                "masters" => $masters,
                "managers" => $managers,
                "operators" => $operators,
            ]);
        }else{
            return view("managers.weekplan", [
                "milestones" => $milestones,
                "comission" => $comission,
                "managerBonusRate" => $managerBonusRate,
                "milestoneBonus" => $milestoneBonus,
                "masters" => $masters,
                "mastersWithOutTeam" => $mastersWithOutTeam,
                "teamWithOutOper" => $teamWithOutOper,
                "teamWithOutTown" => $teamWithOutTown,
                "managers" => $managers,
                "operators" => $operators,
            ]);
        }

    }
    public function statistics()
    {
        access(["can-manager"]);

        $teams = Team::all();

        return view("managers.statistics", [
            "teams" => $teams
        ]);
    }

    public function comissions(Request $request)
    {
        access(["can-manager", "can-owner", "can-host", "can-recruiter"]);

        $masters = Master::all();
        return view("managers.comissions", [
            "masters" => $masters
        ]);
    }

    public function monitoring()
    {
        access(["can-manager", "can-recruiter"]);

        $comissions = Budget::getComissionsPerWeek();
        $masters = Invoice::getMastersNotLoadedInvoiceForWeek(week()->end());

        return view("managers.monitoring", [
            "comissions" => $comissions,
            "masters" => $masters
        ]);
    }

    public function currencyRates()
    {
        access(["can-manager"]);

        $currencies = Currency::all();
        $currencyCount = $currencies->count();
        $currencyRatesPaginator = CurrencyRate::orderByDesc("date")->paginate($currencyCount * 15);
        $currencyRatesGrouped = collect($currencyRatesPaginator->items())->groupBy("date");

        return view("managers.currency-rates", [
            "currencies" => $currencies,
            "currencyRatesPaginator" => $currencyRatesPaginator,
            "currencyRatesGrouped" => $currencyRatesGrouped
        ]);
    }

    public function auth(Manager $manager)
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($manager->user);
            return redirect()->route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }

    public function sync(Request $request)
    {
        $day = 0;
        if ($request->has("day")) {
            $day = intval($request->day);
        }

        $weekdates = daterange(week()->start(), week()->end(), true);
        $date = week()->start();

        foreach ($weekdates as $key => $weekdate) {
            $date = date_format($weekdate, config("app.iso_date"));
            if ($key == $day) break;
        }

        if (isodate() >= $date) {
            if ($day == 0) {
                Artisan::call("load --masters");
                Artisan::call("load --cosmetologists");
                Artisan::call("load --services");
            }

            Artisan::call("load --records --startDate={$date} --endDate={$date}");
            Artisan::call("solve --total-comission --date={$date}");
            Artisan::call("solve --masters-comission --date={$date}");
            Artisan::call("solve --masters-profit --date={$date}");
            Artisan::call("solve --custom-outcomes --date={$date}");
            Artisan::call("solve --managers-profit --date={$date}");
            Artisan::call("solve --operators-profit --date={$date}");
            Artisan::call("solve --masters-penalty --date={$date}");
        }

        $user = Auth::user();
        note("info", "manager:sync", "{$user->account} выполнил обновление из журнала на дату {$date}");

        if ($day >= 6) {
            return redirect()->route("managers.weekplan")->with(["success" => "Неделя обновлена из журнала"]);
        } else {
            $day = $day + 1;
            return redirect()->route("managers.weekplan", ["day" => $day])->with(["info" => "Идет обновление недели ({$day}/7). Пожалуйста, подождите"]);
        }
    }

    public function cosmetologists()
    {
        access(["can-manager"]);

        $cosmetologists = Cosmetologist::all();

        return view("managers.cosmetologists", [
            "cosmetologists" => $cosmetologists
        ]);
    }

    public function masters()
    {
        access(["can-manager"]);
        $budgetType = BudgetType::findByCode("master:unexpected:income");


        $masters = Master::all();


        return view("managers.masters", [
            "masters" => $masters,
            "budgetType" => $budgetType
        ]);
    }

    public function contacts()
    {
        $teams = Team::all();
        $contactTypes = ContactType::all();

        return view("contacts.form", [
            "teams" => $teams,
            "contactTypes" => $contactTypes
        ]);
    }
}
