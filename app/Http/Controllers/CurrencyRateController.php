<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyRateController extends Controller
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
     * @param  \App\Models\CurrencyRate  $currencyRate
     * @return \Illuminate\Http\Response
     */
    public function show(CurrencyRate $currencyRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CurrencyRate  $currencyRate
     * @return \Illuminate\Http\Response
     */
    public function edit(CurrencyRate $currencyRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CurrencyRate  $currencyRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CurrencyRate $currencyRate)
    {
        //
        access(["can-owner", "can-host"]);
        $data = $request->validate([
            'date' => 'required|string|min:3',
            'currencies' => 'required|array'
        ]);
        $date = date(config('app.iso_date'), strtotime($data['date']));
        foreach ($data['currencies'] as $v){

            $currency = Currency::findByCode($v['code']);
            $currencyRate = CurrencyRate::findByCurrencyAndDate($currency,$date);
            $currencyRate->update([
                "currency_id" =>$currency->id,
                "rate" => round($v['value'], 2)
            ]);
        }
        return back()->with(['success' => __('common.saved-success')]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CurrencyRate  $currencyRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(CurrencyRate $currencyRate)
    {
        //
    }
}
