<?php
namespace RoiUp\Zoom\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Support\Facades\Auth;

class EventAuth
{
    /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @param  string|null  $guard
         * @return mixed
         */
        public function handle($request, Closure $next, $guard = null)
        {

            if(!$request->hasHeader('authorization') || $request->header('authorization') !== config('zoom.events_token')){
                Log::warning('ZoomEvents -> Unauthorized request ' . $request->getUri());
                return abort(401);
            }

            return $next($request);
        }
}
