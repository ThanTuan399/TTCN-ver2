<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    /**
     * Get the club this membership belongs to.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the user this membership belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

