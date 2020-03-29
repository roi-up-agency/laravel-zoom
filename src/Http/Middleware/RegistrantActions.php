<?php
namespace RoiUp\Zoom\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Support\Facades\Auth;
use RoiUp\Zoom\Helpers\RegistrantLinks;

class RegistrantActions
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

            if(request()->getMethod() == 'POST'){
                if(request()->get('token') !==  config('zoom.events_token')){
                    return abort(401);
                }
            }else{
                $key = $request->get('key');

                if(empty($key) || !$this->isAuthorized($key)){
                    return abort(401);
                }
            }


            /*if(!$request->hasHeader('authorization') || $request->header('authorization') !== config('zoom.events_token')){
                Log::warning('ZoomEvents -> Unauthorized request ' . $request->getUri());

            }*/

            return $next($request);
        }

        private function isAuthorized($key){
            return RegistrantLinks::getAuthorization($key) === config('zoom.events_token');
        }
}
