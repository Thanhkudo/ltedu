<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminModulePermission
{
    public function handle(Request $request, Closure $next, $module)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('admin.login.form');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        $permissions = [
            'teacher' => ['dashboard', 'classes', 'sessions', 'assignments', 'question-bank', 'question-categories'],
        ];

        $allowedModules = $permissions[$user->role] ?? [];

        if (!in_array($module, $allowedModules)) {
            abort(403, 'Ban khong co quyen truy cap module nay.');
        }

        return $next($request);
    }
}

