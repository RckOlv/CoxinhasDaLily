<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
