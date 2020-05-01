<?php

namespace RoiUp\Zoom\Listeners;

use RoiUp\Zoom\Events\Meeting\MeetingRegistrantApproved;
use RoiUp\Zoom\Events\Meeting\MeetingRegistrantCancelled;
use RoiUp\Zoom\Events\Meeting\MeetingRegistrantCreated;
use RoiUp\Zoom\Events\Meeting\MeetingRegistrantDenied;
use RoiUp\Zoom\Events\Notifications\SendApproveRegistrant;
use RoiUp\Zoom\Events\Notifications\SendCancelRegistrant;
use RoiUp\Zoom\Events\Notifications\SendDeniedRegistrant;
use RoiUp\Zoom\Events\Notifications\SendNewRegistrant;
use RoiUp\Zoom\Events\Notifications\SendRegistrantConfirm;
use RoiUp\Zoom\Models\Eloquent\Meeting;
use RoiUp\Zoom\Models\Eloquent\Registrant as RegistrantModel;
use RoiUp\Zoom\Models\Zoom\Meeting as ZoomMeeting;
use RoiUp\Zoom\Models\Zoom\Registrant;

class RegistrantEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Approved', 'Cancelled', 'Denied'];

    /**
     * Handle registrant created events.
     */
    public function onRegistrantCreated(MeetingRegistrantCreated $event) {

        $meetingId = (string)$event->getObject()['id'];

        $meeting = Meeting::whereZoomId($meetingId)->first();

        if(!empty($meeting)){

            $this->logEvent($event);
            try{
                $zoomRegistrant = new Registrant();
                $zoomRegistrant->create($event->getObject()['registrant']);

                $registrantModel = null;

                switch ($meeting->getSetting(ZoomMeeting::SETTINGS_KEY_REGISTRATION_TYPE)){
                    case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_ALL_OCCURRENCES:
                    case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_MANY_OCCURRENCES:
                        $occurrences = isset($event->getObject()['occurrences']) ? $event->getObject()['occurrences'] : $meeting->occurrences;
                        foreach($occurrences as $occurrence){
                            $occurrenceId = is_array($occurrence) ? $occurrence['occurrence_id'] : $occurrence->occurrence_id;
                            $this->saveRegistrant($zoomRegistrant, $meetingId, $occurrenceId);
                        }
                        break;
                    case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_ONE_OCCURRENCES:
                        $this->saveRegistrant($zoomRegistrant, $meetingId, $event->getObject()['occurrences'][0]['occurrence_id']);
                        break;
                }

                $registrantModel = $this->getRegistrantFromEvent($event);

                event(new SendRegistrantConfirm($registrantModel));

                event(new SendNewRegistrant($registrantModel));

                $this->logFinishEvent();
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }

    }

    private function saveRegistrant($zoomRegistrant, $meetingId, $occurrenceId){
        $registrantModel = new RegistrantModel();
        $registrantModel->fillFromZoomModel($zoomRegistrant, $meetingId, $occurrenceId);

        if(!$registrantModel->isSubscribed()){
            $registrantModel->save();
        }

    }
    /**
     * Handle registrant approved events.
     */
    public function onRegistrantApproved(MeetingRegistrantApproved $event) {

        $registrant = $this->getRegistrantFromEvent($event);

        if(!empty($registrant)){
            $this->logEvent($event);

            try{
                foreach ($registrant->occurrences as $occurrence){
                    if(is_array($occurrence)){
                        $occurrence = (object)$occurrence;
                    }
                    RegistrantModel::whereMeetingId($registrant->meeting_id)->whereRegistrantId($registrant->registrant_id)->whereOccurrenceId($occurrence->occurrence_id)->update(['status' => 'approved']);
                }

                event(new SendApproveRegistrant($registrant));
                $this->logFinishEvent();
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }



    }

    /**
     * Handle registrant cancelled events.
     */
    public function onRegistrantCancelled(MeetingRegistrantCancelled $event) {

        $registrant = $this->getRegistrantFromEvent($event);

        if(!empty($registrant)) {
            $this->logEvent($event);
            try{
                foreach ($registrant->occurrences as $occurrence) {
                    if (is_array($occurrence)) {
                        $occurrence = (object)$occurrence;
                    }
                    RegistrantModel::whereMeetingId($registrant->meeting_id)->whereRegistrantId($registrant->registrant_id)->whereOccurrenceId($occurrence->occurrence_id)->delete();
                }

                event(new SendCancelRegistrant($registrant));

                $this->logFinishEvent();
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }
    }


    /**
     * Handle registrant denied events.
     */
    public function onRegistrantDenied(MeetingRegistrantDenied $event) {

        $registrant = $this->getRegistrantFromEvent($event);

        if(!empty($registrant)) {
            $this->logEvent($event);
            try {
                foreach ($registrant->occurrences as $occurrence) {
                    if (is_array($occurrence)) {
                        $occurrence = (object)$occurrence;
                    }
                    RegistrantModel::whereMeetingId($registrant->meeting_id)->whereRegistrantId($registrant->registrant_id)->whereOccurrenceId($occurrence->occurrence_id)->update(['status' => 'denied']);
                }

                event(new SendDeniedRegistrant($registrant));

                $this->logFinishEvent();
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }
    }



    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {

        foreach($this->actions as $action){
            $events->listen(
                'RoiUp\Zoom\Events\Meeting\\MeetingRegistrant' . $action,
                self::class . '@onRegistrant' . $action
            );
        }

    }

    private function getRegistrantFromEvent($event){

        $zoomRegistrant = new Registrant();
        $zoomRegistrant->create($event->getObject()['registrant']);
        $meetingId = (string)$event->getObject()['id'];

        $registrant = RegistrantModel::whereMeetingId($meetingId)->whereRegistrantId($zoomRegistrant->id)->first();
        if(!empty($registrant)){
            $registrant->occurrences = isset($event->getObject()['occurrences']) ? $event->getObject()['occurrences'] : $registrant->meeting->registrantOccurrences($registrant->registrant_id);
        }


        return $registrant;
    }
}