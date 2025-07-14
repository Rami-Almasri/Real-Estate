<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeFactory> */
    use HasFactory;
    protected $fillable = [

        'address',
        'latitude',
        'longitude',
        'district_id',

    ];
    public function provider()
    {
        return $this->morphOne(User::class, 'userable');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
