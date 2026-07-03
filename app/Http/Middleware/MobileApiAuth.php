<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;

class MobileApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token = MobileApiToken::with('student')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (!$token || !$token->student || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        $request->attributes->set('mobile_student', $token->student);
        $request->attributes->set('mobile_token', $token);

        return $next($request);
    }
}
