<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
    ];

    /**
     * Get the event this participation belongs to.
     */
    public function event()
    {
        return $this->belongsTo(ClubEvent::class, 'event_id');
    }

    /**
     * Get the user this participation belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

