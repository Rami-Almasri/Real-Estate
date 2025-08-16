<?php

namespace App\Http\Resources;

use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'office_id' => $this->office_id,
            'district' => DistrictResourse::make($this->whenLoaded('district')),

            'price' => $this->price,
            'status' => $this->status,
            'type' => $this->type,
            'area' => $this->area,
            'rooms' => $this->rooms,
            'floor' => $this->floor,
            'direction' => $this->direction,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            // 'viewsCount' => $this->view()->count(),
            'averageRating' => $this->averageRating(),
            'viewsCount' => $this->view_count ?? 0,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}