<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $operators = Operator::all();

        return view("operators.index", [
            'operators' => $operators
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

        $operator = Operator::createWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show(Operator $operator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function edit(Operator $operator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Operator $operator)
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

        $operator = $operator->updateWithRelations($data);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Operator $operator)
    {
        access(["can-owner", "can-host"]);

        $operatorId = $operator->id;
        $operatorName = $operator->name;

        $operator->user->delete();
        $operator->delete();

        note("info", "operator:delete", "Удален оператор {$operatorName}", Operator::class, $operatorId);

        return back()->with(['success' => __('common.deleted-success')]);
    }

    public function statistics(Request $request)
    {
        access(["can-operator"]);

        $teams = Auth::user()->teams;
        $team = $request->has('team') ? $teams->find($request->team) : $teams->first();
        $conversion = 0; // todo solve conversion

        return view("operators.statistics", [
            'team' => $team,
            'teams' => $teams,
            'conversion' => $conversion
        ]);
    }

    public function salesplan()
    {
        access(["can-operator"]);

        $operator = Auth::user()->operator;

        $profit = $operator->getProfit(week()->start(), week()->end());
        $lastWeekProfit = $operator->getProfit(week()->previous(week()->start()), week()->previous(week()->end()));
        $points = $operator->getPoints($profit);
        $lastWeekPoints = $operator->getPoints($lastWeekProfit);

        $milestones = collect([
            ['profit' => $lastWeekPoints, 'bonus' => 'Прошлая нед']
        ]);

        $masters = $operator->teams->map(function ($team) {
            return $team->masters;
        })->collapse();

        return view("operators.salesplan", [
            'operator' => $operator,
            'points' => $points,
            'milestones' => $milestones,
            'profit' => $profit,
            'masters' => $masters
        ]);
    }

    public function auth(Operator $operator)
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($operator->user);
            return redirect()->route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }
}
