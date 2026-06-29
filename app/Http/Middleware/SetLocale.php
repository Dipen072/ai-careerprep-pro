<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = 'en';

        if (Auth::check()) {
            $pref = Auth::user()->language_preference;
            // Extract primary language code (e.g. gu_en -> gu)
            $locale = explode('_', $pref)[0];
        } elseif (Session::has('locale')) {
            $locale = explode('_', Session::get('locale'))[0];
        }

        // Validate locale
        if (in_array($locale, ['en', 'hi', 'gu'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
