<?php

namespace App\Http\Controllers;

use App\Jobs\LoadCosmetologistsJob;
use App\Models\Cosmetologist;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CosmetologistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $cosmetologists = Cosmetologist::all();
        $teams = Team::all();

        return view("cosmetologists.index", [
            "cosmetologists" => $cosmetologists,
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
     * @param  \App\Models\Cosmetologist  $cosmetologist
     * @return \Illuminate\Http\Response
     */
    public function show(Cosmetologist $cosmetologist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cosmetologist  $cosmetologist
     * @return \Illuminate\Http\Response
     */
    public function edit(Cosmetologist $cosmetologist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cosmetologist  $cosmetologist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cosmetologist $cosmetologist)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user' => 'required|array',
            'user.account' => 'required|string|min:3',
            'user.password' => 'nullable|string|min:3',
            'user.email' => 'nullable|email',
            'user.phone' => 'nullable|string',
        ]);

        $cosmetologist->update(['team_id' => intval($data['team_id'])]);

        $cosmetologist = $cosmetologist->fresh();

        $userData = [
            'account' => $data['user']['account'],
            'email' => $data['user']['email'],
            'phone' => $data['user']['phone'],
        ];

        if (!empty($data['user']['password'])) {
            $userData['password'] = bcrypt(trim($data['user']['password']));
            $userData['open_password'] = $data['user']['password'];
        }

        $cosmetologist->user->update($userData);

        note("info", "cosmetologist:update", "Обновлен косметолог {$cosmetologist->name}", Cosmetologist::class, $cosmetologist->id);

        return back()->with(['success' => __('common.saved-success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cosmetologist  $cosmetologist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cosmetologist $cosmetologist)
    {
        //
    }

    public function load(Cosmetologist $cosmetologist)
    {
        access(["can-owner", "can-host", "can-manager"]);

        LoadCosmetologistsJob::dispatchNow($cosmetologist->origin_id);

        return back()->with(['success' => __("common.loaded-success")]);
    }

    public function loadAll()
    {
        access(["can-owner", "can-host", "can-manager"]);

        LoadCosmetologistsJob::dispatchNow();

        return back()->with(['success' => __("common.loaded-success")]);
    }

    public function auth(Cosmetologist $cosmetologist)
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        if ($user->isOwner() || $user->isHost()) {
            Auth::login($cosmetologist->user);
            return redirect()->route("dashboard");
        }

        return back()->with(["error" => "Ошибка авторизации"]);
    }
}
