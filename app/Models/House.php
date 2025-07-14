<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class House extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\HouseFactory> */
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'office_id',
        'district_id',
        'status',
        'type',
        'rooms',
        'floor',
        'area',
        'direction',
        'price',
        'longitude',
        'latitude',
    ];
}
