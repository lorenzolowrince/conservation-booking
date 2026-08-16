<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        $authorized = match ($role) {
            User::ROLE_SUPER_ADMIN => $user?->isSuperAdmin(),
            User::ROLE_ADMIN => $user?->isAdmin(),
            default => false,
        };

        if (! $authorized) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
