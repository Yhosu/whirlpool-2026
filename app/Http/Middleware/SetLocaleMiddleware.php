<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Session::has('locale')) {
            \App::setLocale(\Session::get('locale'));
        } else {
            \Session::put('locale', \App::getLocale());
        }

        return $next($request);
    }
}
