<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'avatar',
        'cover_image',
        'founded_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'founded_date' => 'date',
        ];
    }

    /**
     * Get the user who created this club.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the members of this club.
     */
    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }

    /**
     * Get the users who are members of this club.
     */
    public function memberUsers()
    {
        return $this->belongsToMany(User::class, 'club_members')
                    ->withPivot('role', 'status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the posts in this club.
     */
    public function posts()
    {
        return $this->hasMany(ClubPost::class);
    }

    /**
     * Get the events of this club.
     */
    public function events()
    {
        return $this->hasMany(ClubEvent::class);
    }
}

