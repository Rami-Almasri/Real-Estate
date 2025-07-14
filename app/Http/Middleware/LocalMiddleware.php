<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // استخرج فقط أول لغة من Accept-Language، تجاهل الباقي
        $rawLocale = $request->header('Accept-Language');
        $locale = explode(',', $rawLocale)[0]; // "en_US,en;q=0.9,ar;q=0.8" → "en_US"

        // إذا كانت اللغة غير مدعومة، استخدم 'en' كافتراضي
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
        /*
         if ($request->hasHeader('Accept-Language')) {
            // dd($request->header('Accept-Language')  );
            app()->setLocale($request->header('Accept-Language'));
        }
        return $next($request);*/
    }
}
