<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'userable_type',
        'userable_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function userable()
    {
        return $this->morphTo();
    }

    public function preferences()
    {
        return $this->hasMany(Preference::class);
    }

    public function matchNotifications()
    {
        return $this->hasMany(MatchNotification::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /** The office this user owns, if they are an office account (else null). */
    public function getOfficeAttribute(): ?Office
    {
        return $this->isOffice() ? $this->userable : null;
    }

    public function isOffice(): bool
    {
        return $this->userable_type === Office::class;
    }

    public function isAdmin(): bool
    {
        return $this->userable_type === Admin::class;
    }
}
