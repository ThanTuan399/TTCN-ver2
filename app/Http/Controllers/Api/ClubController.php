<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    /**
     * Display a listing of the clubs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Club::with(['creator:id,name,email,avatar', 'memberUsers:id,name,avatar'])
                     ->withCount(['memberUsers', 'posts', 'events']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $clubs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ClubResource::collection($clubs),
            'pagination' => [
                'current_page' => $clubs->currentPage(),
                'last_page' => $clubs->lastPage(),
                'per_page' => $clubs->perPage(),
                'total' => $clubs->total(),
            ],
        ]);
    }

    /**
     * Display the specified club.
     */
    public function show($id): JsonResponse
    {
        $club = Club::with([
            'creator:id,name,email,avatar,student_code',
            'memberUsers:id,name,email,avatar,student_code',
            'posts' => function ($query) {
                $query->where('status', 'approved')
                      ->with(['user:id,name,avatar'])
                      ->withCount(['comments', 'reactions'])
                      ->latest()
                      ->limit(10);
            },
            'events' => function ($query) {
                $query->with(['creator:id,name,avatar'])
                      ->withCount('participants')
                      ->where('start_time', '>=', now())
                      ->orderBy('start_time')
                      ->limit(5);
            },
        ])
        ->withCount(['memberUsers', 'posts', 'events'])
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ClubResource($club),
        ]);
    }

    /**
     * Get active clubs only.
     */
    public function active(): JsonResponse
    {
        $clubs = Club::where('status', 'active')
                     ->with(['creator:id,name,email,avatar'])
                     ->withCount(['memberUsers', 'posts', 'events'])
                     ->latest()
                     ->get();

        return response()->json([
            'success' => true,
            'data' => ClubResource::collection($clubs),
        ]);
    }

    /**
     * Get featured/popular clubs.
     */
    public function featured(): JsonResponse
    {
        $clubs = Club::where('status', 'active')
                     ->with(['creator:id,name,email,avatar'])
                     ->withCount([
                         'members as member_users_count' => function ($query) {
                             $query->where('status', 'approved');
                         },
                         'posts',
                         'events'
                     ])
                     ->orderByDesc('member_users_count')
                     ->limit(6)
                     ->get();

        return response()->json([
            'success' => true,
            'data' => ClubResource::collection($clubs),
        ]);
    }
}

