<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Log;
use RoiUp\Zoom\Http\Middleware\EventAuth;
use RoiUp\Zoom\Events\ZoomEvent;

Route::prefix('zoom')->middleware(EventAuth::class)->group(function () {

    Route::prefix('events')->group(function () {

        $requestData = (object)request()->all();

        $eventClass = isset($requestData->event) ? ZoomEvent::getEventClass($requestData->event) : null;

        Route::post('meetings', function () use ($eventClass, $requestData){

            if($eventClass !== null){
                Log::debug('ZoomEvent Received -> ' . request()->getUri());
                event(new $eventClass($requestData));
            }else{
                Log::debug($requestData->event . ' failed to Dispatch, event not found');
            }

            return response('Transaction Completed', 200)
                ->header('Content-Type', 'text/plain');

        });

    });
});

