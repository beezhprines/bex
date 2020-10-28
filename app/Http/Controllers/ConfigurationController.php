<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
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
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function show(Configuration $configuration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function edit(Configuration $configuration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Configuration $configuration)
    {
        access(["can-owner", "can-host"]);

        switch ($configuration->code) {
            case "bex:manager:milestones":
                $data = ["value" => json_encode($request->value)];
                break;

            case "bex:manager:profit":
                $data = ["value" => intval($request->value) / 100];
                break;

            case "bex:operator:profit":
                $data = ["value" => intval($request->value) / 1000];
                break;

            default:
                $data = $request->all();
                break;
        }

        $configuration->update($data);

        note("info", "configuration:update", "Обновлена конфигурация", Configuration::class, $configuration->id);

        return back()->with(["success" => __("common.saved-success")]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Configuration $configuration)
    {
        //
    }

    public function bonuses()
    {
        access(["can-owner", "can-host"]);

        $bexManagerProfit = Configuration::findByCode("manager:profit");
        $bexManagerMilestones = Configuration::findByCode("manager:milestones");
        $bexOperatorPoint = Configuration::findByCode("operator:point");
        $bexOperatorProfit = Configuration::findByCode("operator:profit");

        return view("configurations.bonuses", [
            "bexManagerProfit" => $bexManagerProfit,
            "bexManagerMilestones" => $bexManagerMilestones,
            "bexOperatorPoint" => $bexOperatorPoint,
            "bexOperatorProfit" => $bexOperatorProfit,
        ]);
    }
}
