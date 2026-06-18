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
        'title',
        'description',
        'cover_image',
        'featured',
        'closed_at',
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

    protected $casts = [
        'featured'  => 'boolean',
        'closed_at' => 'datetime',
        'area'      => 'decimal:2',
        'price'     => 'decimal:2',
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
    public function favortie()
    {
        return $this->hasMany(Favorite::class, 'house_id');
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function matchNotifications()
    {
        return $this->hasMany(MatchNotification::class);
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

    /** Price per square metre — the key signal for the market dashboard. */
    public function getPricePerMeterAttribute(): float
    {
        return $this->area > 0 ? round($this->price / $this->area) : 0;
    }

    /** Always return a usable cover image, falling back to a curated default. */
    public function getCoverAttribute(): string
    {
        if ($this->cover_image) {
            return $this->cover_image;
        }
        $pool = [
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=900&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=900&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=900&q=80',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=900&q=80',
            'https://images.unsplash.com/photo-1430285561322-7808604715df?w=900&q=80',
        ];
        return $pool[$this->id % count($pool)];
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'empty');
    }
}