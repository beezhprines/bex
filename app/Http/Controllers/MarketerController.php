<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetType;
use App\Models\Configuration;
use App\Models\Marketer;
use App\Models\Team;
use Illuminate\Http\Request;

class MarketerController extends Controller
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marketer  $marketer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketer $marketer)
    {
        //
    }


    public function analytics()
    {
        $budgetTypeInstagram = BudgetType::findByCode('marketer:team:instagram:outcome');
        $budgetTypeVK = BudgetType::findByCode('marketer:team:vk:outcome');

        // todo: add dollars, tenge, rubles as in whatsapp video
        return view("marketers.analytics");
    }

    public function diagrams()
    {
        $milestones = collect(json_decode(Configuration::findByCode('manager:milestones')->value, true))
            ->map(function ($milestone) {
                $milestone['bonus'] = null;
                return $milestone;
            });

        $totalComission =  Budget::getComission(week()->start(), week()->end());

        return view("marketers.diagrams", [
            'milestones' => $milestones,
            'totalComission' => $totalComission,
        ]);
    }
}
