<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        access(["can-owner", "can-host"]);

        $cities = City::all();
        $countries = Country::all();

        return view("cities.index", [
            'cities' => $cities,
            'countries' => $countries,
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

        $city = City::create($request->all());

        note("info", "city:create", "Создан город {$city->title}", City::class, $city->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        access(["can-owner", "can-host"]);

        $city->update($request->all());

        note("info", "city:update", "Обновлен город {$city->title}", City::class, $city->id);

        return back()->with([
            'success' => __('common.saved-success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }

    public function updateAll(Request $request)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            'cities' => 'required|array',
            'cities.*.title' => 'required|string',
            'cities.*.code' => 'required|string|min:3',
            'cities.*.country_id' => 'required|exists:countries,id',
        ]);

        foreach ($data['cities'] as $cityId => $cityData) {
            $city = City::find($cityId);
            $city = $city->update($cityData);
        }

        note("info", "city:update", "Обновлены города", City::class);

        return back()->with(['success' => __('common.saved-success')]);
    }
}
