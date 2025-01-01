<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $method = $request->method();
        $url = $request->getPathInfo(); // Extract the path without query parameters

        $data = $request->all();

        Log::channel('app_requests')->info('----------------------------------------------');
        Log::channel('app_requests')->info('--------------- NEW REQUEST ------------------');
        Log::channel('app_requests')->info('METHOD   => ' . $method);
        Log::channel('app_requests')->info('URL      => ' . $url);
        Log::channel('app_requests')->info('DATA     => ', $data);

        return $next($request);
    }
}
