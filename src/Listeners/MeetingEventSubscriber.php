<?php

namespace RoiUp\Zoom\Listeners;

 use RoiUp\Zoom\Events\Meeting\MeetingCreated;
 use RoiUp\Zoom\Events\Meeting\MeetingDeleted;
 use RoiUp\Zoom\Events\Meeting\MeetingUpdated;
 use RoiUp\Zoom\Events\Meeting\MeetingStarted;
 use RoiUp\Zoom\Events\Meeting\MeetingEnded;
 use RoiUp\Zoom\Events\Notifications\SendDeleteOccurrence;
 use RoiUp\Zoom\Models\Eloquent\Meeting;
 use RoiUp\Zoom\Models\Eloquent\Meeting as EloquentModel;
 use RoiUp\Zoom\Models\Eloquent\Occurrence;
 use RoiUp\Zoom\Models\Eloquent\Registrant as RegistrantModel;

 class MeetingEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Updated', 'Deleted', 'Started', 'Ended'];

    /**
     * Handle meeting created events.
     */
    public function onMeetingCreated(MeetingCreated $event) {

        $this->logEvent($event);

        $model = Meeting::whereZoomId($event->getObject()['id'])->first();
        $model->status = 'created';
        $model->save();

        $this->logFinishEvent();
    }

    /**
     * Handle meeting deleted events.
     */
    public function onMeetingDeleted(MeetingDeleted $event) {

        $this->logEvent($event);

        $meeting = Meeting::whereZoomId($event->getObject()['id'])->first();

        $occurrences = isset($event->getObject()['occurrences']) ? $event->getObject()['occurrences'] : $meeting->occurrences;

        foreach($occurrences as $occurrence){

            $item = Occurrence::whereOccurrenceId($occurrence['occurrence_id'])->whereMeetingId($meeting->zoom_id)->first();

            if($item == null){
                continue;
            }

            $item->registrants->each(function($registrant){
                if($registrant->status !== 'denied'){
                    event(new SendDeleteOccurrence($registrant));
                }
                $registrant->delete();
            });

            $item->delete();
        }

        if(Occurrence::whereMeetingId($meeting->zoom_id)->count() == 0){
            $meeting->delete();
        }

        $this->logFinishEvent();
    }

    /**
     * Handle meeting updated events.
     */
    public function onMeetingUpdated(MeetingUpdated $event) {

        $this->logEvent($event);

        $meeting = EloquentModel::whereZoomId($event->getObject()['id'])->first();

        $changes = $event->getObject();

        unset($changes['id']);
        foreach($changes as $field => $value){

            switch ($field){
                case 'recurrence':
                    $meeting->$field = json_encode($value);
                    break;
                case 'settings':
                    $meeting->mergeSettings($value);
                    break;
                case 'occurrences':
                    $meeting->updateOccurrences($value);
                    break;
                default:
                    if(in_array($field, $meeting->getFillable())){
                        $meeting->$field = $value;
                    }
                    break;
            }
        }

        $meeting->duration = $meeting->occurrences[0]['duration'];
        $meeting->start_time = $meeting->occurrences[0]['start_time'];

        $meeting->save();

        $this->logFinishEvent();
    }

    /**
     * Handle meeting started events.
     */
    public function onMeetingStarted(MeetingStarted $event) {

        $this->logEvent($event);
        $this->changeMeetingStatus($event->getObject()['id'], 'started');
        $this->logFinishEvent();
    }

    /**
     * Handle meeting ended events.
     */
    public function onMeetingEnded(MeetingEnded $event) {

        $this->logEvent($event);
        $this->changeMeetingStatus($event->getObject()['id'], 'ended');
        $this->logFinishEvent();
    }

    private function changeMeetingStatus($meetingId, $status){
        Meeting::whereZoomId($meetingId)->update(['status' => $status]);
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