<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateCommentRequest;
use App\Models\ClubMember;
use App\Models\ClubPost;
use App\Models\ClubPostComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubPostCommentController extends Controller
{
    /**
     * Create a comment on a post.
     */
    public function store(CreateCommentRequest $request, $clubId, $postId): JsonResponse
    {
        $user = auth()->user();
        
        $post = ClubPost::where('club_id', $clubId)
                       ->where('status', 'approved')
                       ->findOrFail($postId);

        // Check if user is an approved member of the club
        $member = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->where('status', 'approved')
                           ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải là thành viên đã được duyệt của câu lạc bộ để bình luận',
            ], 403);
        }

        $comment = ClubPostComment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        $comment->load('user:id,name,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Đã bình luận thành công',
            'data' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar,
                ],
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * Get comments for a post.
     */
    public function index(Request $request, $clubId, $postId): JsonResponse
    {
        $post = ClubPost::where('club_id', $clubId)
                       ->where('status', 'approved')
                       ->findOrFail($postId);

        $comments = ClubPostComment::where('post_id', $postId)
                                   ->with('user:id,name,avatar')
                                   ->latest()
                                   ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $comments->map(function ($comment) {
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
            }),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }
}
