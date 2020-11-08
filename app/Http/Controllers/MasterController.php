<?php

namespace App\Http\Controllers;

use App\Jobs\LoadMastersJob;
use App\Jobs\LoadServicesJob;
use App\Models\BudgetType;
use App\Models\Master;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $masters = Master::all();
        $teams = Team::all();

        return view("masters.index", [
            "masters" => $masters,
            "teams" => $teams
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function show(Master $master)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function edit(Master $master)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Master $master)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string',
            'services' => 'required|array',
            'services.*.comission' => 'required|numeric',
            'services.*.conversion' => 'required|in:0,1',
        ]);

        $master->update(['team_id' => intval($data['team_id'])]);

        $master = $master->fresh();

        $userData = [
            'account' => $data['user']['account'],
            'email' => $data['user']['email'],
            'phone' => $data['user']['phone'],
        ];

        if (!empty($data['user']['password'])) {
            $userData['password'] = bcrypt(trim($data['user']['password']));
            $userData['open_password'] = $data['user']['password'];
        }

        $master->user->update($userData);

        foreach ($data['services'] as $serviceId => $serviceData) {
            $service = Service::find($serviceId);

            if (!empty($service)) {
                $service->update([
                    'comission' => $serviceData['comission'],
                    'conversion' => $serviceData['conversion'],
                ]);
            }
        }

        note("info", "master:update", "Обновлен мастер {$master->name}", Master::class, $master->id);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master  $master
     * @return \Illuminate\Http\Response
     */
    public function destroy(Master $master)
    {
        //
    }

    public function statistics()
    {
        access(["can-master"]);

        $master = Auth::user()->master;

        $budget = $master->getBudget(week()->end(), BudgetType::findByCode("master:comission:income")->id);

        $penalty = $master->getPenalty(week()->start(), week()->end());

        $comission = $master->getComission(week()->start(), week()->end());

        return view("masters.statistics", [
            "master" => $master,
            "comission" => $comission,
            "budget" => $budget,
            "penalty" => $penalty
        ]);
    }

    public function load(Master $master)
    {
        access(["can-owner", "can-host", "can-manager"]);

        LoadMastersJob::dispatchNow($master->origin_id);
        LoadServicesJob::dispatchNow($master->origin_id);

        return back()->with(['success' => __("common.loaded-success")]);
    }

    public function loadAll()
    {
        access(["can-owner", "can-host", "can-manager"]);

        LoadMastersJob::dispatchNow();
        LoadServicesJob::dispatchNow();

        return back()->with(['success' => __("common.loaded-success")]);
    }

    public function auth(Master $master)
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($master->user);
            return redirect()->route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }

    public function services()
    {
        access(["can-manager"]);

        $masters = Master::all();

        return view("masters.services", [
            "masters" => $masters
        ]);
    }

    public function servicesUpdate(Request $request, Master $master)
    {
        access(["can-manager"]);

        $data = $request->validate([
            'services.*.comission' => 'required|numeric',
            'services.*.conversion' => 'required|in:0,1',
        ]);

        foreach ($data['services'] as $serviceId => $serviceData) {
            $service = Service::find($serviceId);

            if (!empty($service)) {
                $service->update([
                    'comission' => $serviceData['comission'],
                    'conversion' => $serviceData['conversion'],
                ]);
            }
        }

        note("info", "service:update", "Обновлены услуги мастера {$master->name}", Master::class, $master->id);

        return redirect()->to(url()->previous() . "#master-{$master->id}")->with(['success' => __('common.saved-success')]);
    }
}
