<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\House;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function favorite(array $data)
    {
        $favorite = Favorite::create([
            'user_id' => $data['user_id'],
            'house_id' => $data['house_id']
        ]);

        return $favorite;
    }
    public function unfavorite(Favorite $favorite)
    {
        if (Auth::user()->id !== $favorite->user_id) {
            throw new \Exception('Unauthorized');
        } else {
            $favorite->delete();
            return $favorite;
        }
    }
    public function list()
    {
        $favorites = Favorite::with(['house', 'house.district'])->where('user_id', Auth::user()->id)->get();
        return $favorites;
    }
}