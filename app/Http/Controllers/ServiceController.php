<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Service;
use App\Services\YClientsService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private $yClientsService;

    function __construct(YClientsService $yClientsService)
    {
        $this->yClientsService = $yClientsService;
    }
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
    public function store(Request $request, Master $master)
    {
        access(["can-owner", "can-host", "can-manager"]);

        $data = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'comission' => 'required|numeric',
            'conversion' => 'required|in:0,1',
            'seance_length' => 'required|numeric'
        ]);

        $this->yClientsService->authorize();

        // create service in yclients
        $serviceCategories = $this->yClientsService->getServiceCategory($master->origin_id);

        $serviceCategoryId = $serviceCategories[0]["id"] ?? null;

        if (empty($serviceCategoryId)) {
            return back()->with(['error' => 'Не найдена категория услуг для мастера']);
        }

        $serviceData = $this->yClientsService->createService([
            "title" => $data["title"],
            "category_id" => $serviceCategoryId,
            "price" => $data["price"],
            "staff_id" => $master->origin_id,
            "seance_length" => $data["seance_length"]
        ]);

        // load service from yclients
        Service::seed([$serviceData]);

        $service = Service::findByOriginId($serviceData["id"]);

        if (empty($service)) {
            return back()->with(['error' => 'Произошла ошибка при добавлении услуги']);
        }

        $service->update([
            "comission" => $data["comission"],
            "conversion" => $data["conversion"]
        ]);

        return back()->with(['success' => "Услуга добавлена"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
    }
}
