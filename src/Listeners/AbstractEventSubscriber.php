<?php

namespace RoiUp\Zoom\Listeners;

use Illuminate\Support\Facades\Log;
use RoiUp\Zoom\Models\Eloquent\EventLog;

abstract class AbstractEventSubscriber
{
    private $logId = null;

    protected function logEvent($event){

        $eventLog = new EventLog();
        $eventLog->event = $event->getEvent();
        $eventLog->object_id = $event->getObject()['id'];
        $eventLog->host_id = $event->getAccountId();
        if(!empty($event->getOperator())) {
            $eventLog->operator = $event->getOperator() . ' - ' . $event->getOperatorId();
        }
        $eventLog->object_data = json_encode($event->getObject());
        $eventLog->payload = json_encode($event->getPayload());
        $eventLog->status = 'logging';
        $eventLog->save();

        $this->logId = $eventLog->id;

        Log::debug('ZoomEvent Received -> ' . $event->getEvent());
        Log::debug('Account Id -> ' . $event->getAccountId());
        if(!empty($event->getOperator())){
            Log::debug('Operator -> ' . $event->getOperator() . ' - ' . $event->getOperatorId());
        }
        Log::debug('Object Data -> ' . json_encode($event->getObject()));
    }

    protected function logFinishEvent(){
        Log::debug('Zoom Transaction Completed');
        EventLog::whereId($this->logId)->update(['status' => 'logged']);
    }

    protected function logFailedEvent(\Exception $exception){
        Log::debug('Zoom Transaction Error');
        EventLog::whereId($this->logId)->update(['status' => 'failed', 'error_trace' => $exception->getMessage() . PHP_EOL .$exception->getTraceAsString()]);
    }
    protected function logNotFoundEvent(){
        Log::debug('Zoom Entity not found');
    }

}