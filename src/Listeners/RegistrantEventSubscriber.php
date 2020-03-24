<?php

namespace RoiUp\Zoom\Listeners;

 use RoiUp\Zoom\Events\Meeting\MeetingCreated;
 use RoiUp\Zoom\Events\Meeting\MeetingDeleted;
 use RoiUp\Zoom\Events\Meeting\MeetingRegistrantApproved;
 use RoiUp\Zoom\Events\Meeting\MeetingRegistrantCancelled;
 use RoiUp\Zoom\Events\Meeting\MeetingRegistrantCreated;
 use RoiUp\Zoom\Events\Meeting\MeetingRegistrantDenied;
 use RoiUp\Zoom\Events\Meeting\MeetingUpdated;
 use RoiUp\Zoom\Events\Meeting\MeetingStarted;
 use RoiUp\Zoom\Events\Meeting\MeetingEnded;
 use RoiUp\Zoom\Events\Notifications\SendApproveRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendCancelRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendDeniedRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendRegistrantConfirm;
 use RoiUp\Zoom\Models\Zoom\Registrant;
 use RoiUp\Zoom\Models\Eloquent\Registrant as RegistrantModel;

 class RegistrantEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Approved', 'Cancelled', 'Denied'];

    /**
     * Handle registrant created events.
     */
    public function onRegistrantCreated(MeetingRegistrantCreated $event) {

        $this->logEvent($event);
        $zoomRegistrant = new Registrant();
        $zoomRegistrant->create($event->getObject()['registrant']);

        $meetingId = $event->getObject()['id'];

        $occurrenceId = isset($event->getObject()['occurrences']) ? $event->getObject()['occurrences'][0]['occurrence_id'] : null;

        $registrantModel = new RegistrantModel();
        $registrantModel->fillFromZoomModel($zoomRegistrant, $meetingId, $occurrenceId);

        $registrantModel->save();
        
        event(new SendRegistrantConfirm($registrantModel));

        $this->logFinishEvent();
    }

    /**
     * Handle registrant approved events.
     */
    public function onRegistrantApproved(MeetingRegistrantApproved $event) {

        $this->logEvent($event);


        $registrant = $this->getRegistrantFromEvent($event);

        $registrant->status = 'approved';
        $registrant->save();

        event(new SendApproveRegistrant($registrant));

        $this->logFinishEvent();
    }

    /**
     * Handle registrant cancelled events.
     */
    public function onRegistrantCancelled(MeetingRegistrantCancelled $event) {

        $this->logEvent($event);
        $registrant = $this->getRegistrantFromEvent($event);
        $registrant->delete();

        event(new SendCancelRegistrant($registrant));

        $this->logFinishEvent();
    }


    /**
     * Handle registrant denied events.
     */
    public function onRegistrantDenied(MeetingRegistrantDenied $event) {

        $this->logEvent($event);
        $registrant = $this->getRegistrantFromEvent($event);
        $registrant->status = 'denied';
        $registrant->save();

        event(new SendDeniedRegistrant($registrant));
        
        $this->logFinishEvent();
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
        $meetingId = $event->getObject()['id'];
        $occurrenceId = isset($event->getObject()['occurrences']) ? $event->getObject()['occurrences'][0]['occurrence_id'] : null;

        return RegistrantModel::whereMeetingId($meetingId)->whereRegistrantId($zoomRegistrant->id)->whereOccurrenceId($occurrenceId)->first();
    }
}