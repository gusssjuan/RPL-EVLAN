<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  (e.g., 'admin', 'user', 'vendor' or 'admin|user')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Expand any pipe-separated roles: 'admin|user' -> ['admin', 'user']
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $subRole) {
                $allowedRoles[] = trim($subRole);
            }
        }

        $userRole = $user->role?->nama;

        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}

