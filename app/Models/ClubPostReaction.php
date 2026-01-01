<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubPostReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'type',
    ];

    /**
     * Get the post this reaction belongs to.
     */
    public function post()
    {
        return $this->belongsTo(ClubPost::class, 'post_id');
    }

    /**
     * Get the user who created this reaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

