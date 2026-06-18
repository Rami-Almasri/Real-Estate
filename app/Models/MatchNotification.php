<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_id',
        'preference_id',
        'score',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'score'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function preference()
    {
        return $this->belongsTo(Preference::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
