<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Session;

class Locale
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->settings) {
            App::setLocale($request->user()->settings->language);
        }

        return $next($request);
    }
 }
