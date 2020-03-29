<?php

namespace RoiUp\Zoom\Listeners;

 use Illuminate\Support\Facades\Mail;
 use RoiUp\Zoom\Events\Notifications\SendApproveRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendCancelRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendDeleteOccurrence;
 use RoiUp\Zoom\Events\Notifications\SendDeniedRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendNewRegistrant;
 use RoiUp\Zoom\Models\Eloquent\Meeting;
 use RoiUp\Zoom\Models\Zoom\Meeting as ZoomMeeting;
 use RoiUp\Zoom\Notifications\RegistrantConfirm;
 use RoiUp\Zoom\Events\Notifications\SendRegistrantConfirm;
 use RoiUp\Zoom\Helpers\RegistrantLinks;
 use RoiUp\Zoom\Notifications\SimpleEmail;

 class NotificationsEventSubscriber
{

    /**
     * Handle registrant created events.
     */
    public function onSendRegistrantConfirm(SendRegistrantConfirm $event) {

        $registrant = $event->registrant;
        $meeting    = $registrant->meeting;
        $host       = $meeting->host;
        
        $arrDate = [];
        foreach($registrant->occurrences as $occurrence){
            if(is_array($occurrence)){
                $occurrence = (object)$occurrence;
            }
            $date = $this->getFormattedDate($occurrence->start_time);

            switch ($meeting->getSetting(ZoomMeeting::SETTINGS_KEY_REGISTRATION_TYPE)) {
                case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_ALL_OCCURRENCES:
                case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_ONE_OCCURRENCES:
                    $arrDate[] = $date;
                    break;
                case ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_MANY_OCCURRENCES:
                    $registrant->occurrence_id = $occurrence->occurrence_id;
                    $link = RegistrantLinks::generateActionLink('cancel', $registrant);
                    $link = '<a href="'. $link. '" target="_blank">Cancelar suscripción para esta recurrencia</a>';
                    $arrDate[] = $this->getFormattedDate($occurrence->start_time) . ' - ' . $link;
                    break;
            }

        }

        $data = [
            'firstName'     => $registrant->first_name,
            'lastName'      => $registrant->last_name,
            'topic'         => $meeting->topic,
            'ownerEmail'    => $host->email,
            'date'          => $arrDate,
            'timezone'      => $meeting->timezone,
            'calendarLinks' => RegistrantLinks::getCalendarLinks($meeting, $registrant->registrant_id),
            'joinUrl'       => $registrant->join_url,
            'password'      => !empty($meeting->password) ? $meeting->password : '',
        ];
        if($meeting->getSetting(ZoomMeeting::SETTINGS_KEY_REGISTRATION_TYPE) !== ZoomMeeting::SETTINGS_REGISTRATION_TYPE_ONCE_MANY_OCCURRENCES){
            $data['cancelRegistrationLink']   = RegistrantLinks::generateActionLink('cancel', $registrant, false);
        }

        $data = array_merge($this->initEmailData(), $data);

        Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send(new RegistrantConfirm($data));
    }

     public function onSendNewRegistrant(SendNewRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;
         $host       = $meeting->host;

         $arrDate = [];
         foreach($registrant->occurrences as $occurrence){
             if(is_array($occurrence)){
                 $occurrence = (object)$occurrence;
             }
             $arrDate[] = $this->getFormattedDate($occurrence->start_time);
         }

         $text = trans('zoom::emails.registration_new_text', ['topic' => $meeting->topic, 'date' => implode(', ', $arrDate) . ' ' . $meeting->timezone, 'registrant_email' => $registrant->email, 'registrant_name' => $registrant->fullName()]);

         $data = [
             'name'         => $host->first_name . ' ' . $host->last_name,
             'text'         => $text,
         ];

         if($meeting->ifManualApproveNeeded()){
             $data['approveLink']  = RegistrantLinks::generateActionLink('approve', $registrant, false);
             $data['denyLink']     = RegistrantLinks::generateActionLink('deny', $registrant, false);
         }
         
         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_new_subject', ['topic' => $meeting->topic]);
         Mail::to($host->email, $host->first_name . ' ' . $host->last_name)->send($mail);
     }

     public function onSendApproveRegistrant(SendApproveRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;

         foreach($registrant->occurrences as $occurrence){
             if(is_array($occurrence)){
                 $occurrence = (object)$occurrence;
             }
             $arrDate[] = $this->getFormattedDate($occurrence->start_time);
         }

         $text = trans('zoom::emails.registration_approved_text', ['topic' => $meeting->topic, 'date' => implode(', ', $arrDate) . ' ' . $meeting->timezone]);

         $data = [
             'name'     => $registrant->first_name . ' ' . $registrant->last_name,
             'text'     => $text,
         ];
         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_approved_subject', ['topic' => $meeting->topic]);
         Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send($mail);
     }

     public function onSendCancelRegistrant(SendCancelRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;

         foreach($registrant->occurrences as $occurrence){
             if(is_array($occurrence)){
                 $occurrence = (object)$occurrence;
             }
             $arrDate[] = $this->getFormattedDate($occurrence->start_time);
         }

         $text = trans('zoom::emails.registration_cancelled_text', ['topic' => $meeting->topic, 'date' => implode(', ', $arrDate) . ' ' . $meeting->timezone]);

         $data = [
             'name'     => $registrant->first_name . ' ' . $registrant->last_name,
             'text'     => $text,
         ];
         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_cancelled_subject', ['topic' => $meeting->topic]);
         Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send($mail);
     }

     public function onSendDeniedRegistrant(SendDeniedRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;

         foreach($registrant->occurrences as $occurrence){
             if(is_array($occurrence)){
                 $occurrence = (object)$occurrence;
             }
             $arrDate[] = $this->getFormattedDate($occurrence->start_time);
         }

         $text = trans('zoom::emails.registration_denied_text', ['topic' => $meeting->topic, 'date' => implode(', ', $arrDate) . ' ' . $meeting->timezone]);

         $data = [
             'name'     => $registrant->first_name . ' ' . $registrant->last_name,
             'text'     => $text,
         ];
         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_denied_subject', ['topic' => $meeting->topic]);
         Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send($mail);
     }

     public function onOccurrenceDeleted(SendDeleteOccurrence $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;
         $occurrence = $registrant->occurrence;


         $text = trans('zoom::emails.registration_deleted_text', ['topic' => $meeting->topic, 'date' => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone]);

         $data = [
             'name'     => $registrant->first_name . ' ' . $registrant->last_name,
             'text'     => $text,
         ];
         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_deleted_subject', ['topic' => $meeting->topic]);
         Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send($mail);
     }
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            SendRegistrantConfirm::class,
            self::class . '@onSendRegistrantConfirm'
        );

        $events->listen(
            SendNewRegistrant::class,
            self::class . '@onSendNewRegistrant'
        );

        $events->listen(
            SendApproveRegistrant::class,
            self::class . '@onSendApproveRegistrant'
        );

        $events->listen(
            SendCancelRegistrant::class,
            self::class . '@onSendCancelRegistrant'
        );

        $events->listen(
            SendDeniedRegistrant::class,
            self::class . '@onSendDeniedRegistrant'
        );

        $events->listen(
            SendDeleteOccurrence::class,
            self::class . '@onOccurrenceDeleted'
        );


    }

    private function getFormattedDate($zoomTime){

        $format = config('zoom.emails_date_format');

        return date($format, strtotime($zoomTime));
    }

    private function initEmailData(){
        return [
            'logoUrl'       => config('zoom.emails_logo_url'),
            'footerContent' => trans('zoom::emails.footer_html_content')
        ];
    }
}