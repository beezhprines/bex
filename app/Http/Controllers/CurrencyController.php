<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $currencies = Currency::all();
        $currencyCount = $currencies->count();
        $currencyRatesPaginator = CurrencyRate::orderByDesc("date")->paginate($currencyCount * 15);
        $currencyRatesGrouped = collect($currencyRatesPaginator->items())->groupBy("date");
        return view("currencies.index", [
            'currencies' => $currencies,
            "currencyRatesPaginator" => $currencyRatesPaginator,
            "currencyRatesGrouped" => $currencyRatesGrouped
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

        $currency = Currency::create($request->all());

        note("info", "currency:create", "Создана валюта {$currency->title}", Currency::class, $currency->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        access(["can-owner", "can-host"]);

        $currency->update($request->all());

        note("info", "currency:update", "Обновлена валюта {$currency->title}", Currency::class, $currency->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        //
    }

    public function updateAll(Request $request)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'currencies' => 'required|array',
            'currencies.*.title' => 'required|string',
            'currencies.*.code' => 'required|string|min:3',
        ]);

        foreach ($data['currencies'] as $currencyId => $currencyData) {
            $currency = Currency::find($currencyId);
            $currency = $currency->update($currencyData);
        }

        note("info", "currency:update", "Обновлены валюты", Currency::class);

        return back()->with(['success' => __('common.saved-success')]);
    }
}
