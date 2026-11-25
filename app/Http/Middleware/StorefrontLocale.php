<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class StorefrontLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');
        if (in_array($locale, ['id', 'en'], true)) {
            $request->session()->put('storefront_locale', $locale);
        }

        $previous = App::getLocale();
        App::setLocale($request->session()->get('storefront_locale', 'id'));

        try {
            return $next($request);
        } finally {
            App::setLocale($previous);
        }
    }
}
