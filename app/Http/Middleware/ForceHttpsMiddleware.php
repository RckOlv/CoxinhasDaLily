<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Forwarded-Proto') === 'https') {
            $request->setSecureScheme(true);
            $request->server->set('HTTPS', 'on');
        }

        return $next($request);
    }
}
