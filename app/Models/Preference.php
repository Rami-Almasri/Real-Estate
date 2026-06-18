<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'district_id',
        'type',
        'min_rooms',
        'max_price',
        'min_area',
        'label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_price' => 'decimal:2',
        'min_area'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function matches()
    {
        return $this->hasMany(MatchNotification::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
