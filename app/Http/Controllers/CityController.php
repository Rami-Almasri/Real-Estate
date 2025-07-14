<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CityResourse;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Services\CityService;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $cityService;
    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }
    public function index()
    {
        $cities = $this->cityService->index();
        $result = CityResourse::collection($cities);
        return ResponseHelper::SuccessResponse($result);
    }

    /************************************************* */
    public function store(StoreCityRequest $request)
    {
        $city = $this->cityService->store($request->validated());
        $result = CityResourse::make($city);
        return ResponseHelper::SuccessResponse($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
    }

    /**                                  */
    public function update(UpdateCityRequest $request, City $city)
    {
        $city = $this->cityService->update($request->validated(), $city);
        $result = CityResourse::make($city);
        return ResponseHelper::SuccessResponse($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {

        $city = $this->cityService->destroy($city);
        $result = CityResourse::make($city);
        return ResponseHelper::SuccessResponse($result);
    }
}