<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Cookie;

class SetLocale
{
    private const SUPPORTED = ['ar', 'en', 'fr'];
    private const COOKIE    = 'locale';
    private const DEFAULT   = 'ar';

    public function handle(Request $request, Closure $next)
    {
        // Priority: 1) locale cookie (plain, unencrypted)  2) session  3) default
        $locale = $request->cookie(self::COOKIE);

        if (!in_array($locale, self::SUPPORTED)) {
            $locale = $request->hasSession()
                ? $request->session()->get(self::COOKIE, self::DEFAULT)
                : self::DEFAULT;
        }

        if (!in_array($locale, self::SUPPORTED)) {
            $locale = self::DEFAULT;
        }

        App::setLocale($locale);

        $response = $next($request);

        // After the route/controller ran, re-read the app locale.
        // If the language-switch route called App::setLocale() the updated
        // value is reflected here and will be stamped on the outgoing cookie.
        $finalLocale = App::getLocale();
        if (!in_array($finalLocale, self::SUPPORTED)) {
            $finalLocale = self::DEFAULT;
        }

        // Plain, unencrypted locale cookie (excluded from EncryptCookies).
        // SameSite=Lax + Secure=false → works on HTTP and HTTPS navigation.
        $cookie = new Cookie(
            self::COOKIE,
            $finalLocale,
            time() + (60 * 60 * 24 * 365), // 1 year
            '/',
            null,
            false,        // Secure=false: works on HTTP too
            false,        // HttpOnly=false
            false,
            Cookie::SAMESITE_LAX
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
