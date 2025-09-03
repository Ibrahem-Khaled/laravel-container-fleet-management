<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditContext
{
    public function handle(Request $request, Closure $next): Response
    {
        Context::add([
            'trace_id'   => (string) Str::uuid(),
            'user_id'    => optional($request->user())->getAuthIdentifier(),
            'ip'         => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
        ]);

        return $next($request);
    }
}
