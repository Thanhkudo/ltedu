<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ckFinderAuthentication
{
    public function handle($request, Closure $next)
    {
        config(['ckfinder.authentication' => function () {
            $user = Auth::user();

            return $user && in_array($user->role, ['admin', 'teacher'], true);
        }]);

        return $next($request);
    }
}
