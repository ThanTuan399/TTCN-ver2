<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('Authorization');
        
        // Remove "Bearer " prefix if present
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token không được cung cấp',
            ], 401);
        }

        // Hash token to compare with database
        $hashedToken = hash('sha256', $token);
        $user = User::where('api_token', $hashedToken)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ',
            ], 401);
        }

        // Check if user is banned
        if ($user->status === 'banned') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa',
            ], 403);
        }

        // Set authenticated user
        auth()->setUser($user);

        return $next($request);
    }
}
