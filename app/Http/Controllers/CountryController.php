<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $countries = Country::all();
        $currencies = Currency::all();

        return view("countries.index", [
            'countries' => $countries,
            'currencies' => $currencies,
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

        $country = Country::create($request->all());

        note("info", "country:create", "Создана страна {$country->title}", Country::class, $country->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit(Country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        access(["can-owner", "can-host"]);

        $country->update($request->all());

        note("info", "country:update", "Обновлена страна {$country->title}", Country::class, $country->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        //
    }

    public function updateAll(Request $request)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'countries' => 'required|array',
            'countries.*.title' => 'required|string',
            'countries.*.code' => 'required|string|min:3',
            'countries.*.currency_id' => 'required|exists:currencies,id',
        ]);

        foreach ($data['countries'] as $countryId => $countryData) {
            $country = Country::find($countryId);
            $country = $country->update($countryData);
        }

        note("info", "country:update", "Обновлены страны", Country::class);

        return back()->with(['success' => __('common.saved-success')]);
    }
}
