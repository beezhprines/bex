<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Contact;
use App\Models\Cosmetologist;
use App\Models\Master;
use App\Models\Operator;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Session;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host", "can-manager"]);

        $teams = Team::all();
        $operators = Operator::all();
        $cities = City::all();

        return view("teams.index", [
            'teams' => $teams,
            'operators' => $operators,
            'cities' => $cities
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
        access(["can-owner", "can-host", "can-manager"]);

        $team = Team::create($request->all());

        $startDate = week()->monday(isodate());
        $endDate = week()->sunday(isodate());

        Artisan::call("seed --contacts --startDate={$startDate} --endDate={$endDate}");
        note("info", "team:create", "Создана команда {$team->title}", Team::class, $team->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        access(["can-owner", "can-host", "can-manager"]);

        $team->update($request->all());

        note("info", "team:update", "Обновлена команда {$team->title}", Team::class, $team->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        //
    }

    public function updateAll(Request $request)
    {
        access(["can-owner", "can-host", "can-manager"]);

        $data = $request->validate([
            'teams' => 'required|array',
            'teams.*.title' => 'required|string',
            'teams.*.operator_id' => 'required',
            'teams.*.city_id' => 'required|exists:cities,id',
        ]);

        foreach ($data['teams'] as $teamId => $teamData) {

            if((is_int($teamData['operator_id']) && $teamData['operator_id']=0)){
                $teamData['operator_id'] = null;
            }
            $team = Team::find($teamId);
            $team = $team->update($teamData);
        }

        note("info", "team:update", "Обновлены команды", Team::class);

        return redirect("/teams")->with(['success' => __('common.saved-success')]);
    }
    public function archivateTeam(Request $request)
    {

        access(["can-owner", "can-host", "can-manager"]);
        $teamId = $request->team;
        $masters = Master::all();
        $cosmetologists = Cosmetologist::all();
        $team = Team::find($teamId);
        if (!empty($team->operator)){
            return redirect("/teams?")->with('warning','Ошибка! '.$team->title.' имеет оператора');;
        }
        foreach ($masters as $master){
            if(intval($master->team_id )== intval($teamId)){
                return redirect("/teams?")->with('warning','Ошибка! '.$team->title.' имеет мастера '.$master->name);;
            }
        }
        foreach ($cosmetologists as $cosmetologist){
            if(intval($cosmetologist->team_id )== intval($teamId)){
                return redirect("/teams?")->with('warning','Ошибка! '.$team->title.' имеет косметолога '.$cosmetologist->name);;
            }
        }
        $team->delete();
        note("info", "team:archivate", "archivateTeam  id=".($request->team), Team::class);
        return redirect("/teams")->with(['success' => __('common.saved-success')]);


    }
}
