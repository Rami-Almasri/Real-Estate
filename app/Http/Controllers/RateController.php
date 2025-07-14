<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;
use App\Http\Resources\RatingResource;
use App\Services\RatingService;
use Exception;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $ratingService;
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateRequest $request)
    {
        try {
            $store = $this->ratingService->store($request->validated());
            $result = RatingResource::make($store);
            return ResponseHelper::SuccessResponse($result, 'adding rating successfuly');
        } catch (Exception $e) {

            return ResponseHelper::FailureResponse(null, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rate $rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRateRequest $request, Rate $rate)
    {


        try {
            $update = $this->ratingService->update($request->validated(), $rate);
            $result = RatingResource::make($update);
            return ResponseHelper::SuccessResponse($result, 'update rating successfuly');
        } catch (Exception $e) {

            return ResponseHelper::FailureResponse(null, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        //
    }
}
