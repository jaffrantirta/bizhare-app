<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedInvestor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Your identity has not been verified yet. Please complete ID verification.',
            ], 403);
        }

        if (!$user->has_initial_deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your initial deposit to access investment features.',
            ], 403);
        }

        return $next($request);
    }
}
