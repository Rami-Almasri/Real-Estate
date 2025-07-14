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
    public $sum;
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
    public function rate()
    {
        return $this->hasMany(Rate::class, 'house_id');
    }
    public function view()
    {
        return $this->hasMany(View::class, 'house_id');
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function averageRating(): float
    {
        $ratings = $this->rate()->pluck('rating');

        $count = $ratings->count();
        if ($count === 0) {
            return 0;
        }

        $sum = $ratings->sum();
        return $sum / $count;
    }
}
