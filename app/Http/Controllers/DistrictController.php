<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\DistrictResourse;
use App\Models\District;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Services\DistrictService;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $districtService;
    public function __construct(DistrictService $districtService)
    {
        $this->districtService = $districtService;
    }
    public function index()
    {
        $district = $this->districtService->index();
        $result = DistrictResourse::collection($district);
        return ResponseHelper::SuccessResponse($result);
    }


    public function store(StoreDistrictRequest $request)
    {
        //
        $district = $this->districtService->store($request->validated());
        $result = DistrictResourse::make($district);
        return ResponseHelper::SuccessResponse($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(District $district)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistrictRequest $request, District $district)
    {
        $district = $this->districtService->update($request->validated(), $district);
        $result = DistrictResourse::make($district);
        return ResponseHelper::SuccessResponse($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district = $this->districtService->destroy($district);
        $result = DistrictResourse::make($district);
        return ResponseHelper::SuccessResponse($result);
    }
}