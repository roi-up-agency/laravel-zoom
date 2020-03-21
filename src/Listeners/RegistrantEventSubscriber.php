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

 class RegistrantEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Approved', 'Cancelled', 'Denied'];

    /**
     * Handle registrant created events.
     */
    public function onRegistrantCreated(MeetingRegistrantCreated $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    /**
     * Handle registrant approved events.
     */
    public function onRegistrantApproved(MeetingRegistrantApproved $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    /**
     * Handle registrant cancelled events.
     */
    public function onRegistrantCancelled(MeetingRegistrantCancelled $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    
    /**
     * Handle registrant denied events.
     */
    public function onRegistrantDenied(MeetingRegistrantDenied $event) {

        $this->logEvent($event);

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

}