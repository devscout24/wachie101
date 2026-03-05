<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShowLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::alert("show the request: " ,[
            "url" => $request->fullUrl(),
            "method" => $request->method(),
            "headers" => $request->headers->all(),
            "body" => $request->all(),
            'query' => $request->query(),
            "ip" => $request->ip(),
            "userAgent"=> $request->userAgent(),
            "session" => $request?->session()?->all(),
        ]);
        return $next($request);
    }
}
