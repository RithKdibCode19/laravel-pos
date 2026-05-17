<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Get the locale from session or use default
        $locale = session('lang', config('app.locale'));
        
        // Validate if the locale is supported
        if (!in_array($locale, ['en', 'km'])) {
            $locale = config('app.locale');
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }
} 