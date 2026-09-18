<?php

namespace App\Http\Middleware;

use App\Models\Masjid;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveMasjid
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('masjid');
        if ($slug instanceof Masjid) {
            $masjid = $slug;
        } else {
            if (! $slug) {
                $host = explode(':', $request->getHost())[0];
                $base = parse_url(config('app.url'), PHP_URL_HOST) ?: 'isetial.test';
                if ($host !== $base && str_ends_with($host, '.'.$base)) {
                    $slug = str($host)->before('.'.$base)->toString();
                }
            }
            $masjid = Masjid::where('slug', $slug)->where('is_active', true)->firstOrFail();
        }
        app()->instance('activeMasjid', $masjid);
        view()->share('activeMasjid', $masjid);
        if ($request->route('masjid')) {
            $request->route()->setParameter('masjid', $masjid);
        }

        return $next($request);
    }
}
