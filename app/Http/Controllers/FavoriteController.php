<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreFavoriteHouseRequest;
use App\Http\Resources\HouseResource;
use App\Models\Favorite;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\House;
use App\Services\FavoriteService;
use Exception;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $favoriteService;
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index()
    {
        try {
            $favorites = $this->favoriteService->list();
            $result = FavoriteResource::collection($favorites);
            return ResponseHelper::SuccessResponse($result);
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }
    public function store(StoreFavoriteHouseRequest $request)
    {
        try {

            $favortie = $this->favoriteService->favorite($request->validated());

            return ResponseHelper::SuccessResponse(null, "the house.id has been added to favorites");
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        try {
            $favorite = $this->favoriteService->unfavorite($favorite);

            return ResponseHelper::SuccessResponse(null, "the house.id has been removed from favorites");
        } catch (Exception $e) {
            return ResponseHelper::FailureResponse(null, $e->getMessage(), 500);
        }
    }
}