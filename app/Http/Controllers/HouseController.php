<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Helpers\ResponseHelper;
use App\Http\Requests\FilterHouserRequest;
use App\Http\Requests\StoreFavoriteHouseRequest;
use App\Models\House;
use App\Http\Requests\StoreHouseRequest;
use App\Http\Requests\UpdateHouseRequest;
use App\Http\Resources\HouseResource;
use App\Models\Favorite;
use App\Services\HousesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class HouseController extends Controller
{

    private $houseService;
    public function __construct(HousesService $houseService)
    {
        $this->houseService = $houseService;
    }


    public function index()
    {
        try {

            $houses = $this->houseService->index();

            $result = HouseResource::collection($houses);

            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function store(StoreHouseRequest $request)
    {
        try {

            $house = $this->houseService->create($request->validated());
            $result = HouseResource::make($house);

            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {

            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function show(House $house)
    {
        try {

            $houses = $this->houseService->show($house);
            $result = HouseResource::make($houses);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateHouseRequest $request, House $house)
    {
        try {

            $updated = $this->houseService->update($house, $request->validated());
            $result = HouseResource::make($updated);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }
    public function delete(House $house)
    {
        try {

            $deleted = $this->houseService->delete($house);
            $result = HouseResource::make($deleted);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }
    public function filter(FilterHouserRequest $request)
    {
        $filter = $this->houseService->filter($request->validated());
        $result = HouseResource::collection($filter);
        return ResponseHelper::SuccessResponse($result);
    }
}