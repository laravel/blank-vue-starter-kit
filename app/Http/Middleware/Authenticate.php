<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;


class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('auth.redirect');
    }
}
