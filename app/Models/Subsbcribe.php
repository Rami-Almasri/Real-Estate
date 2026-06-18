<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsbcribe extends Model
{
    /** @use HasFactory<\Database\Factories\SubsbcribeFactory> */
    use HasFactory;

    protected $fillable = [
        'office_id',
        'plan',
        'price',
        'listing_limit',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'is_active'     => 'boolean',
        'price'         => 'decimal:2',
        'listing_limit' => 'integer',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /** Active = flagged active and not past its end date. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereDate('end_date', '>=', now());
    }

    public function getIsValidAttribute(): bool
    {
        return $this->is_active && $this->end_date && $this->end_date->endOfDay()->isFuture();
    }

    public function getDaysLeftAttribute(): ?int
    {
        return $this->end_date ? max(0, (int) round(now()->startOfDay()->diffInDays($this->end_date, false))) : null;
    }
}
