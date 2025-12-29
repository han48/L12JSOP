<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class NormalizeLocale
{
    public function handle($request, Closure $next)
    {
        $header = $request->header('Accept-Language');

        if ($header) {
            // Take only the first locale before comma
            $locale = explode(',', $header)[0];

            // Normalize: replace hyphens with underscores
            $locale = str_replace('-', '_', $locale);

            // Validate: fallback if invalid
            if (!preg_match('/^[a-zA-Z_]+$/', $locale)) {
                $locale = config('app.locale');
            }

            $request->headers->set('Accept-Language', $locale);
        }

        return $next($request);
    }
}
