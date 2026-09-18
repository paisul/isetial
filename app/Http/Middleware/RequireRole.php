<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        $masjidId = app()->bound('activeMasjid') ? app('activeMasjid')->id : null;
        if (! $user->isSuperAdmin() && ! collect($roles)->contains(fn ($role) => $user->hasRole($role, $masjidId))) {
            abort(403);
        }

        return $next($request);
    }
}
