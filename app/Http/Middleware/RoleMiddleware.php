<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Нет доступа.');
        }

        // если одна строка "Admin,Employer" — разобьём по запятой
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Нет доступа.');
        }

        return $next($request);
    }
}
