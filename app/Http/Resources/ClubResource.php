<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'cover_image' => $this->cover_image,
            'founded_date' => $this->founded_date?->format('Y-m-d'),
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Creator information
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                    'avatar' => $this->creator->avatar,
                    'student_code' => $this->creator->student_code ?? null,
                ];
            }),
            
            // Counts
            'members_count' => $this->when(
                isset($this->member_users_count) || isset($this->memberUsers_count),
                fn() => $this->member_users_count ?? $this->memberUsers_count ?? 0
            ) ?? $this->whenLoaded('memberUsers', fn() => $this->memberUsers->count()),
            
            'posts_count' => $this->when(
                isset($this->posts_count),
                fn() => $this->posts_count
            ) ?? $this->whenLoaded('posts', fn() => $this->posts->count()),
            
            'events_count' => $this->when(
                isset($this->events_count),
                fn() => $this->events_count
            ) ?? $this->whenLoaded('events', fn() => $this->events->count()),
            
            // Members list (when loaded)
            'members' => $this->whenLoaded('memberUsers', function () {
                return $this->memberUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'student_code' => $user->student_code ?? null,
                        'role' => $user->pivot->role ?? null,
                        'status' => $user->pivot->status ?? null,
                        'joined_at' => $user->pivot->joined_at ?? null,
                    ];
                });
            }),
            
            // Recent posts (when loaded)
            'recent_posts' => $this->whenLoaded('posts', function () {
                return $this->posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'content' => $post->content,
                        'is_anonymous' => $post->is_anonymous,
                        'status' => $post->status,
                        'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                        'author' => $post->is_anonymous ? null : [
                            'id' => $post->user->id,
                            'name' => $post->user->name,
                            'avatar' => $post->user->avatar,
                        ],
                        'comments_count' => $post->comments_count ?? 0,
                        'reactions_count' => $post->reactions_count ?? 0,
                    ];
                });
            }),
            
            // Upcoming events (when loaded)
            'upcoming_events' => $this->whenLoaded('events', function () {
                return $this->events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'start_time' => $event->start_time->format('Y-m-d H:i:s'),
                        'end_time' => $event->end_time?->format('Y-m-d H:i:s'),
                        'location' => $event->location,
                        'participants_count' => $event->participants_count ?? 0,
                        'creator' => [
                            'id' => $event->creator->id,
                            'name' => $event->creator->name,
                            'avatar' => $event->creator->avatar,
                        ],
                    ];
                });
            }),
        ];
    }
}

