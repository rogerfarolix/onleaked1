<?php

namespace App\Http\Middleware;

use App\Models\AdminLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            AdminLog::create([
                'user_id'    => $request->user()->id,
                'action'     => $request->method() . ' ' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata'   => [
                    'route' => $request->route()?->getName(),
                    'query' => $request->query->all(),
                ],
            ]);
        }

        return $response;
    }
}
