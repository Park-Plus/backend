<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app('db')->connection()->enableQueryLog();
        $req = $next($request);
        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            if (isset($query['query'])) {
                Log::info('[QUERY] ' . $query['query']);
            }
        }

        return $req;
    }
}
