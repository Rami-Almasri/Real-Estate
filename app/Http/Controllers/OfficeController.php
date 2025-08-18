<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Helpers\ResponseHelper;
use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Services\OfficeService;
use Exception;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{


    public function __construct(protected OfficeService $officeService) {}

    public function index()
    {
        try {
            $offices = $this->officeService->index();

            $result = OfficeResource::collection($offices);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function store(StoreOfficeRequest $request)
    {
        try {
            $offices = $this->officeService->create($request->validated());

            $result = OfficeResource::make($offices);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function show(Office $office)
    {
        try {
            $offices = $this->officeService->show($office);

            $result = OfficeResource::collection($offices);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateOfficeRequest $request, Office $office)
    {
        try {
            $updated = $this->officeService->update($request->validated(), $office);


            $result = OfficeResource::make($updated);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }


    public function delete(Office $office)
    {
        try {
            $delte = $this->officeService->delete($office);

            $result = OfficeResource::make($delte);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }
    public function detils()
    {
        $myhouse = $this->officeService->info();
        return Response()->json([

            'data' => [
                'myhouse' => $myhouse['total_houses'],
                'all_houses' => $myhouse['all_houses'],
                'occupied_rent' => $myhouse['occupied_rent'],
                'occupied_sale' => $myhouse['occupied_sale']
            ],
            'msg' => 'success return data',
            'success' => true

        ]);
    }
}
