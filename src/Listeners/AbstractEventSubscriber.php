<?php

namespace RoiUp\Zoom\Listeners;

use Illuminate\Support\Facades\Log;

abstract class AbstractEventSubscriber
{

    protected function logEvent($event){

        Log::debug('ZoomEvent Received -> ' . $event->getEvent());
        Log::debug('Account Id -> ' . $event->getAccountId());
        if(!empty($event->getOperator())){
            Log::debug('Operator -> ' . $event->getOperator() . ' - ' . $event->getOperatorId());
        }
        Log::debug('Object Data -> ' . json_encode($event->getObject()));
    }

    protected function logFinishEvent(){
        Log::debug('Zoom Transaction Completed');
    }
    protected function logNotFoundEvent(){
        Log::debug('Zoom Entity not found');
    }

}