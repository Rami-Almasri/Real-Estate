<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'house_id',
        'type',
        'party_name',
        'party_phone',
        'party_national_id',
        'amount',
        'payment_cycle',
        'start_date',
        'end_date',
        'due_date',
        'status',
        'notes',
        'reference',
        'pdf_path',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'start_date' => 'date',
        'end_date'   => 'date',
        'due_date'   => 'date',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    /** Contracts whose due date falls within the next $days days. */
    public function scopeDueWithin($query, int $days = 14)
    {
        return $query->where('status', 'active')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->whereDate('due_date', '>=', now()->subDays(3));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->status === 'active' && $this->due_date->isPast();
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        return $this->due_date ? (int) round(now()->startOfDay()->diffInDays($this->due_date, false)) : null;
    }
}
