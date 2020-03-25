<?php

namespace RoiUp\Zoom\Listeners;

 use Illuminate\Support\Facades\Mail;
 use RoiUp\Zoom\Events\Notifications\SendApproveRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendCancelRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendDeleteOccurrence;
 use RoiUp\Zoom\Events\Notifications\SendDeniedRegistrant;
 use RoiUp\Zoom\Events\Notifications\SendNewRegistrant;
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
        $occurrence = $registrant->occurrence;
        $host       = $meeting->host;

        $data = [
            'firstName'     => $registrant->first_name,
            'lastName'      => $registrant->last_name,
            'topic'         => $meeting->topic,
            'ownerEmail'    => $host->email,
            'date'          => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone,
            'calendarLinks' => RegistrantLinks::getCalendarLinks($meeting, $registrant->registrant_id),
            'joinUrl'       => $registrant->join_url,
            'password'      => !empty($meeting->password) ? $meeting->password : '',
            'cancelRegistrationLink'      => RegistrantLinks::generateActionLink('cancel', $registrant),
        ];

        $data = array_merge($this->initEmailData(), $data);

        Mail::to($registrant->email, $registrant->first_name . ' ' . $registrant->last_name)->send(new RegistrantConfirm($data));
    }

     public function onSendNewRegistrant(SendNewRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;
         $occurrence = $registrant->occurrence;
         $host       = $meeting->host;

         $text = trans('zoom::emails.registration_new_text', ['topic' => $meeting->topic, 'date' => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone, 'registrant_email' => $registrant->email, 'registrant_name' => $registrant->fullName()]);

         $data = [
             'name'         => $host->first_name . ' ' . $host->last_name,
             'text'         => $text,
             'approveLink'  => RegistrantLinks::generateActionLink('approve', $registrant),
             'denyLink'     => RegistrantLinks::generateActionLink('deny', $registrant),
         ];

         $data = array_merge($this->initEmailData(), $data);

         $mail = new SimpleEmail($data);
         $mail->subject = trans('zoom::emails.registration_new_subject', ['topic' => $meeting->topic]);
         Mail::to($host->email, $host->first_name . ' ' . $host->last_name)->send($mail);
     }

     public function onSendApproveRegistrant(SendApproveRegistrant $event) {

         $registrant = $event->registrant;
         $meeting    = $registrant->meeting;
         $occurrence = $registrant->occurrence;


         $text = trans('zoom::emails.registration_approved_text', ['topic' => $meeting->topic, 'date' => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone]);

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
         $occurrence = $registrant->occurrence;


         $text = trans('zoom::emails.registration_cancelled_text', ['topic' => $meeting->topic, 'date' => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone]);

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
         $occurrence = $registrant->occurrence;


         $text = trans('zoom::emails.registration_denied_text', ['topic' => $meeting->topic, 'date' => $this->getFormattedDate($occurrence->start_time) . ' ' . $meeting->timezone]);

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