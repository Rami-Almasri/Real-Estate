<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    /** @use HasFactory<\Database\Factories\FavoriteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_id',
    ];
    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
