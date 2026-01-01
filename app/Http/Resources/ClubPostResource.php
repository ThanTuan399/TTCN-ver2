<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubPostResource extends JsonResource
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
            'content' => $this->content,
            'is_anonymous' => $this->is_anonymous,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Author (hidden if anonymous)
            'author' => $this->when(!$this->is_anonymous, function () {
                return $this->whenLoaded('user', function () {
                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'avatar' => $this->user->avatar,
                    ];
                });
            }),
            
            // Club info
            'club' => $this->whenLoaded('club', function () {
                return [
                    'id' => $this->club->id,
                    'name' => $this->club->name,
                ];
            }),
            
            // Counts
            'comments_count' => $this->when(
                isset($this->comments_count),
                fn() => $this->comments_count
            ) ?? $this->whenLoaded('comments', fn() => $this->comments->count()),
            
            'reactions_count' => $this->when(
                isset($this->reactions_count),
                fn() => $this->reactions_count
            ) ?? $this->whenLoaded('reactions', fn() => $this->reactions->count()),
            
            // Comments (when loaded)
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'avatar' => $comment->user->avatar,
                        ],
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            
            // Reactions (when loaded)
            'reactions' => $this->whenLoaded('reactions', function () {
                return $this->reactions->map(function ($reaction) {
                    return [
                        'id' => $reaction->id,
                        'type' => $reaction->type,
                        'user' => [
                            'id' => $reaction->user->id,
                            'name' => $reaction->user->name,
                            'avatar' => $reaction->user->avatar,
                        ],
                    ];
                });
            }),
        ];
    }
}
