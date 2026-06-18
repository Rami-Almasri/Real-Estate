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
    protected $appends = ['name'];

    public function provider()
    {
        return $this->morphOne(User::class, 'userable');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
    public function houses()
    {
        return $this->hasMany(House::class);
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subsbcribe::class, 'office_id');
    }

    /** The current, valid subscription for this office (if any). */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->whereDate('end_date', '>=', now())
            ->latest('end_date')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return (bool) $this->activeSubscription();
    }

    /** Display name pulled from the linked provider user, falling back to address. */
    public function getNameAttribute(): string
    {
        return $this->provider?->name ?? ('مكتب ' . $this->address);
    }
}
