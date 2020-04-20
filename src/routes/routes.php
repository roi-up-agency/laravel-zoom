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
use RoiUp\Zoom\Http\Middleware\RegistrantActions;
use RoiUp\Zoom\Http\Controllers\RegistrantController;
use RoiUp\Zoom\Events\ZoomEvent;

Route::prefix('zoom')->group(function () {
    Route::prefix('actions')->middleware(RegistrantActions::class)->group(function () {
        Route::prefix('registrant')->group(function () {
            Route::post('add', RegistrantController::class . '@add')->name('registrant_add');
            Route::get('approve', RegistrantController::class . '@approve')->name('registrant_approve');
            Route::get('deny', RegistrantController::class . '@deny')->name('registrant_deny');
            Route::get('cancel', RegistrantController::class . '@cancel')->name('registrant_cancel');
        });
    });

    Route::prefix('events')->middleware(EventAuth::class)->group(function () {

        $requestData = (object)request()->all();

        $eventClass = isset($requestData->event) ? ZoomEvent::getEventClass($requestData->event) : null;

        Route::post('meetings', function () use ($eventClass, $requestData){

            if($eventClass !== null){
                Log::debug('ZoomEvent Received -> ' . request()->getUri());
                $data = request()->all();
                if(isset($data['event'])){
                    $entryLog = $data['event'];
                    if(isset($data['payload']['object'])){
                        $entryLog .= ' -- ' . $data['payload']['object']['id'];
                    }
                    Log::debug($entryLog);
                }

                event(new $eventClass($requestData));

            }else{
                Log::debug($requestData->event . ' failed to Dispatch, event not found');
            }

            return response('Transaction Completed', 200)
                ->header('Content-Type', 'text/plain');

        });

    });
});

