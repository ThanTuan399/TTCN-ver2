<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\ClubMemberController;
use App\Http\Controllers\Api\ClubPostCommentController;
use App\Http\Controllers\Api\ClubPostController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes (public)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Club routes (public - không cần auth)
    Route::get('/clubs', [ClubController::class, 'index']);
    Route::get('/clubs/active', [ClubController::class, 'active']);
    Route::get('/clubs/featured', [ClubController::class, 'featured']);
    Route::get('/clubs/{id}', [ClubController::class, 'show']);
    
    // Protected routes (cần authentication)
    Route::middleware('auth.api')->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        // Club member routes
        Route::post('/clubs/{clubId}/join', [ClubMemberController::class, 'join']);
        Route::post('/clubs/{clubId}/leave', [ClubMemberController::class, 'leave']);
        Route::get('/clubs/{clubId}/membership-status', [ClubMemberController::class, 'status']);
        
        // Club post routes
        Route::post('/clubs/{clubId}/posts', [ClubPostController::class, 'store']);
        Route::get('/clubs/{clubId}/posts', [ClubPostController::class, 'index']);
        Route::get('/clubs/{clubId}/posts/{postId}', [ClubPostController::class, 'show']);
        // Admin actions on posts
        Route::post('/clubs/{clubId}/posts/{postId}/approve', [ClubPostController::class, 'approve']);
        Route::delete('/clubs/{clubId}/posts/{postId}', [ClubPostController::class, 'destroy']);
        
        // Club post comment routes
        Route::post('/clubs/{clubId}/posts/{postId}/comments', [ClubPostCommentController::class, 'store']);
        Route::get('/clubs/{clubId}/posts/{postId}/comments', [ClubPostCommentController::class, 'index']);
        // Club member management by admins
        Route::post('/clubs/{clubId}/members/{memberId}/approve', [ClubMemberController::class, 'approve']);
        Route::delete('/clubs/{clubId}/members/{memberId}', [ClubMemberController::class, 'destroy']);
    });
});

