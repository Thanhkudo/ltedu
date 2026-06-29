<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login.form');
        }

        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            abort(403, 'Ban khong co quyen truy cap trang quan tri.');
        }

        return $next($request);
    }
}
