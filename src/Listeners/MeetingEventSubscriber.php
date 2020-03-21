<?php

namespace RoiUp\Zoom\Listeners;

 use RoiUp\Zoom\Events\Meeting\MeetingCreated;
 use RoiUp\Zoom\Events\Meeting\MeetingDeleted;
 use RoiUp\Zoom\Events\Meeting\MeetingUpdated;
 use RoiUp\Zoom\Events\Meeting\MeetingStarted;
 use RoiUp\Zoom\Events\Meeting\MeetingEnded;

 class MeetingEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Updated', 'Deleted', 'Started', 'Ended'];

    /**
     * Handle meeting created events.
     */
    public function onMeetingCreated(MeetingCreated $event) {

        $this->logEvent($event);

        $meeting = new \RoiUp\Zoom\Models\Zoom\Meeting();
        $meeting->create((array)$event->getObject());
        dd($meeting);
        $this->logFinishEvent();
    }

    /**
     * Handle meeting deleted events.
     */
    public function onMeetingDeleted(MeetingDeleted $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    /**
     * Handle meeting updated events.
     */
    public function onMeetingUpdated(MeetingUpdated $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    /**
     * Handle meeting started events.
     */
    public function onMeetingStarted(MeetingStarted $event) {

        $this->logEvent($event);

        $this->logFinishEvent();
    }

    /**
     * Handle meeting ended events.
     */
    public function onMeetingEnded(MeetingEnded $event) {

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
                'RoiUp\Zoom\Events\Meeting\\Meeting' . $action,
                self::class . '@onMeeting' . $action
            );
        }

    }

}