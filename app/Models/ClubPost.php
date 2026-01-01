<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'user_id',
        'content',
        'is_anonymous',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
        ];
    }

    /**
     * Get the club this post belongs to.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the user who created this post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments on this post.
     */
    public function comments()
    {
        return $this->hasMany(ClubPostComment::class, 'post_id');
    }

    /**
     * Get the reactions on this post.
     */
    public function reactions()
    {
        return $this->hasMany(ClubPostReaction::class, 'post_id');
    }
}

