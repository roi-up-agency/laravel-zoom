<?php

namespace RoiUp\Zoom\Listeners;

use RoiUp\Zoom\Events\Meeting\MeetingCreated;
use RoiUp\Zoom\Events\Meeting\MeetingDeleted;
use RoiUp\Zoom\Events\Meeting\MeetingEnded;
use RoiUp\Zoom\Events\Meeting\MeetingStarted;
use RoiUp\Zoom\Events\Meeting\MeetingUpdated;
use RoiUp\Zoom\Events\Notifications\SendDeleteOccurrence;
use RoiUp\Zoom\Events\User\UserVerified;
use RoiUp\Zoom\Models\Eloquent\Host;
use RoiUp\Zoom\Models\Eloquent\Meeting;
use RoiUp\Zoom\Models\Eloquent\Meeting as EloquentModel;
use RoiUp\Zoom\Models\Eloquent\Occurrence;

class MeetingEventSubscriber extends AbstractEventSubscriber
{

    private $actions = ['Created', 'Updated', 'Deleted', 'Started', 'Ended'];

    /**
     * Handle meeting created events.
     */
    public function onMeetingCreated(MeetingCreated $event) {


        if($event->getObject()['type'] === 4){
            $host = Host::whereHostId($event->getObject()['host_id'])->whereInvitationStatus('pending')->first();
            if(!empty($host)){
                $this->logEvent($event);
                try{
                    $host->invitation_status = 'accepted';
                    $host->save();
                    event(new UserVerified($host));
                    $this->logFinishEvent();
                }catch (\Exception $exception){
                    $this->logFailedEvent($exception);
                }
            }else{
                $this->logNotFoundEvent();
            }
        }else{
            $model = Meeting::whereZoomId($event->getObject()['id'])->first();
            if(!empty($model)){

                $this->logEvent($event);
                try{
                    $model->status = 'created';
                    $model->save();

                    $this->logFinishEvent();
                }catch (\Exception $exception){
                    $this->logFailedEvent($exception);
                }

            }else{
                $this->logNotFoundEvent();
            }

        }


    }

    /**
     * Handle meeting deleted events.
     */
    public function onMeetingDeleted(MeetingDeleted $event) {

        $meeting = Meeting::whereZoomId($event->getObject()['id'])->first();
        if(!empty($meeting)){
            $this->logEvent($event);
            try{
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
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }



    }

    /**
     * Handle meeting updated events.
     */
    public function onMeetingUpdated(MeetingUpdated $event) {

        $meeting = EloquentModel::whereZoomId($event->getObject()['id'])->first();
        if(!empty($meeting)) {
            $this->logEvent($event);
            try{
                $changes = $event->getObject();

                unset($changes['id']);
                foreach ($changes as $field => $value) {

                    switch ($field) {
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
                            if (in_array($field, $meeting->getFillable())) {
                                $meeting->$field = $value;
                            }
                            break;
                    }
                }

                $meeting->duration = $meeting->occurrences[0]['duration'];
                $meeting->start_time = $meeting->occurrences[0]['start_time'];

                $meeting->save();
                $this->logFinishEvent();
            }catch (\Exception $exception){
                $this->logFailedEvent($exception);
            }
        }else{
            $this->logNotFoundEvent();
        }
    }

    /**
     * Handle meeting started events.
     */
    public function onMeetingStarted(MeetingStarted $event) {

        $this->changeMeetingStatus($event->getObject()['id'], 'started', $event);

    }

    /**
     * Handle meeting ended events.
     */
    public function onMeetingEnded(MeetingEnded $event) {

        $this->changeMeetingStatus($event->getObject()['id'], 'ended', $event);

    }

    private function changeMeetingStatus($meetingId, $status, $event){
        $model = Meeting::whereZoomId($meetingId)->first();
        if(!empty($model)){

            $this->logEvent($event);
            try{

                $model->status = $status;
                $model->save();

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
                'RoiUp\Zoom\Events\Meeting\\Meeting' . $action,
                self::class . '@onMeeting' . $action
            );
        }

    }

}