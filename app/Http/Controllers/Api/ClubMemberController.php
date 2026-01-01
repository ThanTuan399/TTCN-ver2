<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubMemberController extends Controller
{
    /**
     * Join a club (request to join).
     */
    public function join(Request $request, $clubId): JsonResponse
    {
        $user = auth()->user();
        $club = Club::findOrFail($clubId);

        // Check if user is already a member
        $existingMember = ClubMember::where('club_id', $clubId)
                                   ->where('user_id', $user->id)
                                   ->first();

        if ($existingMember) {
            if ($existingMember->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã là thành viên của câu lạc bộ này',
                ], 400);
            }

            if ($existingMember->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu tham gia của bạn đang chờ duyệt',
                ], 400);
            }

            if ($existingMember->status === 'left') {
                // Allow re-joining if previously left
                $existingMember->status = 'pending';
                $existingMember->role = 'member';
                $existingMember->joined_at = null;
                $existingMember->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã gửi yêu cầu tham gia lại câu lạc bộ',
                    'data' => $existingMember,
                ]);
            }
        }

        // Create new membership request
        $member = ClubMember::create([
            'club_id' => $clubId,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'pending',
            'joined_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi yêu cầu tham gia câu lạc bộ. Vui lòng chờ duyệt.',
            'data' => $member,
        ], 201);
    }

    /**
     * Leave a club.
     */
    public function leave(Request $request, $clubId): JsonResponse
    {
        $user = auth()->user();
        
        $member = ClubMember::where('club_id', $clubId)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải là thành viên của câu lạc bộ này',
            ], 404);
        }

        // Check if user is owner
        if ($member->role === 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Chủ sở hữu không thể rời khỏi câu lạc bộ',
            ], 403);
        }

        // Update status to 'left'
        $member->status = 'left';
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã rời khỏi câu lạc bộ thành công',
        ]);
    }

    /**
     * Get membership status of current user for a club.
     */
    public function status($clubId): JsonResponse
    {
        $user = auth()->user();
        
        $member = ClubMember::where('club_id', $clubId)
                            ->where('user_id', $user->id)
                            ->first();

        if (!$member) {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_member' => false,
                    'status' => null,
                    'role' => null,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_member' => true,
                'status' => $member->status,
                'role' => $member->role,
                'joined_at' => $member->joined_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Approve a membership request (admin/owner only).
     */
    public function approve(Request $request, $clubId, $memberId): JsonResponse
    {
        $user = auth()->user();

        $admin = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->whereIn('role', ['admin', 'owner'])
                           ->where('status', 'approved')
                           ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền duyệt thành viên trong câu lạc bộ này',
            ], 403);
        }

        $member = ClubMember::where('club_id', $clubId)->where('id', $memberId)->firstOrFail();

        if ($member->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Thành viên đã được duyệt trước đó',
            ], 400);
        }

        $member->status = 'approved';
        $member->joined_at = now();
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt thành viên thành công',
            'data' => $member,
        ]);
    }

    /**
     * Remove a member from a club (admin/owner only).
     */
    public function destroy(Request $request, $clubId, $memberId): JsonResponse
    {
        $user = auth()->user();

        $admin = ClubMember::where('club_id', $clubId)
                           ->where('user_id', $user->id)
                           ->whereIn('role', ['admin', 'owner'])
                           ->where('status', 'approved')
                           ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền quản lý thành viên trong câu lạc bộ này',
            ], 403);
        }

        $member = ClubMember::where('club_id', $clubId)->where('id', $memberId)->firstOrFail();

        if ($member->role === 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa chủ sở hữu câu lạc bộ',
            ], 403);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thành viên khỏi câu lạc bộ',
        ]);
    }
}
