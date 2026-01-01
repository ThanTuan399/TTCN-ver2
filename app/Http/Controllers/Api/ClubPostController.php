<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreatePostRequest;
use App\Http\Resources\ClubPostResource;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubPostController extends Controller
{
    /**
     * Create a new post in a club.
     */
    public function store(CreatePostRequest $request, $clubId): JsonResponse
    {
        $user = auth()->user();
        $club = Club::findOrFail($clubId);

        // Check if user is an approved member
        $member = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->where('status', 'approved')
                           ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải là thành viên đã được duyệt của câu lạc bộ để đăng bài',
            ], 403);
        }

        $post = ClubPost::create([
            'club_id' => $clubId,
            'user_id' => $user->id,
            'content' => $request->content,
            'is_anonymous' => $request->is_anonymous ?? false,
            'status' => 'pending', // Cần duyệt trước khi hiển thị
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng bài thành công. Bài đăng đang chờ duyệt.',
            'data' => new ClubPostResource($post->load('user', 'club')),
        ], 201);
    }

    /**
     * Get posts in a club.
     */
    public function index(Request $request, $clubId): JsonResponse
    {
        $club = Club::findOrFail($clubId);
        $user = auth()->user();

        $query = ClubPost::where('club_id', $clubId)
                         ->with(['user:id,name,avatar', 'club:id,name'])
                         ->withCount(['comments', 'reactions']);

        // Check if user is member/admin/owner to see pending posts
        $member = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->whereIn('role', ['admin', 'owner'])
                           ->where('status', 'approved')
                           ->first();

        if (!$member) {
            // Only show approved posts for non-admin users
            $query->where('status', 'approved');
        }

        // Filter by status if admin
        if ($member && $request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $posts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ClubPostResource::collection($posts),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Get a single post.
     */
    public function show($clubId, $postId): JsonResponse
    {
        $post = ClubPost::where('club_id', $clubId)
                       ->with(['user:id,name,avatar', 'club:id,name', 'comments.user:id,name,avatar', 'reactions'])
                       ->withCount(['comments', 'reactions'])
                       ->findOrFail($postId);

        // Check if user can view (approved or is admin)
        $user = auth()->user();
        $member = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->whereIn('role', ['admin', 'owner'])
                           ->where('status', 'approved')
                           ->first();

        if ($post->status !== 'approved' && !$member) {
            return response()->json([
                'success' => false,
                'message' => 'Bài đăng không tồn tại hoặc chưa được duyệt',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ClubPostResource($post),
        ]);
    }

    /**
     * Approve a pending post (admin/owner only).
     */
    public function approve(Request $request, $clubId, $postId): JsonResponse
    {
        $user = auth()->user();
        $club = Club::findOrFail($clubId);

        $member = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->whereIn('role', ['admin', 'owner'])
                           ->where('status', 'approved')
                           ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền duyệt bài trong câu lạc bộ này',
            ], 403);
        }

        $post = ClubPost::where('club_id', $clubId)->findOrFail($postId);

        if ($post->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Bài đăng đã được duyệt trước đó',
            ], 400);
        }

        $post->status = 'approved';
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt bài đăng thành công',
            'data' => new ClubPostResource($post->load('user', 'club')),
        ]);
    }

    /**
     * Delete a post (admin/owner or author).
     */
    public function destroy(Request $request, $clubId, $postId): JsonResponse
    {
        $user = auth()->user();
        $post = ClubPost::where('club_id', $clubId)->findOrFail($postId);

        $isAdmin = ClubMember::where('club_id', $clubId)
                             ->where('user_id', $user->id)
                             ->whereIn('role', ['admin', 'owner'])
                             ->where('status', 'approved')
                             ->exists();

        if (!$isAdmin && $post->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bài đăng này',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa bài đăng thành công',
        ]);
    }
}
