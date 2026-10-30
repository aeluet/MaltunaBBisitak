<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class RestrictToInternalNetwork
{
    /**
     * Allow the request only from the school's configured networks.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $networks = config('bisitak.internal_networks');

        if ($networks !== [] && ! IpUtils::checkIp((string) $request->ip(), $networks)) {
            abort(Response::HTTP_FORBIDDEN, 'Eremu hau ikastetxeko saretik bakarrik erabil daiteke.');
        }

        return $next($request);
    }
}
