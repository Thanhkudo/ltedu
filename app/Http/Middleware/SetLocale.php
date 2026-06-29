<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Apply the visitor's selected language for the current request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale', config('app.locale', 'en'));

        if (!in_array($locale, ['en', 'vi'], true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
