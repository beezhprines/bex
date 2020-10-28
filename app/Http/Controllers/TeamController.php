<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Operator;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $team = Team::create($request->all());

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
        $data = $request->validate([
            'teams' => 'required|array',
            'teams.*.title' => 'required|string',
            'teams.*.operator_id' => 'required|exists:operators,id',
            'teams.*.city_id' => 'required|exists:cities,id',
            'teams.*.premium_rate' => 'required|regex:/^\d+([\,]\d+)*([\.]\d+)?$/',
        ]);

        foreach ($data['teams'] as $teamId => $teamData) {
            $team = Team::find($teamId);
            $teamData['premium_rate'] = floatval(str_replace(",", ".", $teamData['premium_rate']));
            $team = $team->update($teamData);
        }

        note("info", "team:update", "Обновлены команды", Team::class);

        return back()->with(['success' => __('common.saved-success')]);
    }
}
