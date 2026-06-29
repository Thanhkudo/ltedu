<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ForceAdminLocale
{
    public function handle(Request $request, Closure $next)
    {
        App::setLocale('vi');

        return $next($request);
    }
}
