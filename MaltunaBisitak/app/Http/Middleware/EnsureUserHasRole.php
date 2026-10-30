<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Usage in routes: ->middleware('role:admin,harrera').
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(Role::from(...), $roles);

        if (! $request->user()?->hasRole(...$allowedRoles)) {
            abort(Response::HTTP_FORBIDDEN, 'Ez duzu baimenik orrialde hau ikusteko.');
        }

        return $next($request);
    }
}
