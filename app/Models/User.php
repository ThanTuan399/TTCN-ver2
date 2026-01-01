<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_code',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
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

    /**
     * Get the clubs created by this user.
     */
    public function createdClubs()
    {
        return $this->hasMany(Club::class, 'created_by');
    }

    /**
     * Get the club memberships of this user.
     */
    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class);
    }

    /**
     * Get the clubs this user is a member of.
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
                    ->withPivot('role', 'status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the posts created by this user.
     */
    public function clubPosts()
    {
        return $this->hasMany(ClubPost::class);
    }

    /**
     * Get the comments created by this user.
     */
    public function clubPostComments()
    {
        return $this->hasMany(ClubPostComment::class);
    }

    /**
     * Get the reactions created by this user.
     */
    public function clubPostReactions()
    {
        return $this->hasMany(ClubPostReaction::class);
    }

    /**
     * Get the events created by this user.
     */
    public function createdEvents()
    {
        return $this->hasMany(ClubEvent::class, 'created_by');
    }

    /**
     * Get the event participations of this user.
     */
    public function eventParticipations()
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * Get the events this user is participating in.
     */
    public function events()
    {
        return $this->belongsToMany(ClubEvent::class, 'event_participants')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
