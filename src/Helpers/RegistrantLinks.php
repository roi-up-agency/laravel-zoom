<?php

namespace RoiUp\Zoom\Helpers;

use RoiUp\Zoom\Models\Eloquent\Registrant;

class RegistrantLinks{

    const CALENDAR_TYPE_GOOGLE       = 'google';
    const CALENDAR_TYPE_ICALENDAR    = 'icalendar';
    const CALENDAR_TYPE_DOWNLOAD     = 'download';
    const CALENDAR_TYPE_YAHOO        = 'yahoo';

    public static function generateActionLink($action, Registrant $registrant, $includeOccurrence = true){


        return route('registrant_' . $action) . '?key='. self::generateKey($registrant, $includeOccurrence);

    }

    public static function generateKey(Registrant $registrant, $includeOccurrence = true){

        $data = [
            'meeting_id'        => $registrant->meeting_id,
            'registrant_id'     => $registrant->registrant_id,
            'registrant_email'  => $registrant->email,
            'authorization'     => config('zoom.events_token')
        ];

        if($includeOccurrence){
            $data['occurrence_id'] = $registrant->occurrence_id;
        }

        return encrypt(json_encode($data));
    }

    public static function getAuthorization($key){

        $decrypted = decrypt($key);
        $keyObject = json_decode($decrypted);

        return isset($keyObject->authorization) ? json_decode($decrypted)->authorization : null;

    }

    public static function getData($key){

        $decrypted = decrypt($key);
        $arrData = json_decode($decrypted);
        unset($arrData->authorization);

        return $arrData;

    }

    public static function getCalendarLinks($meeting, $registrantId, $type = null){

        $url = config('zoom.links_base_url') . 'meeting/attendee/';

        $registrationUrl = explode('/', $meeting->registration_url);
        $meetingSegment = end($registrationUrl) . '/';

        $url .= $meetingSegment;

        if($type !== null){

            return $url . self::getCalendarEndpoint($type, $registrantId);
        }else{
            $types = [self::CALENDAR_TYPE_DOWNLOAD, self::CALENDAR_TYPE_GOOGLE, self::CALENDAR_TYPE_ICALENDAR, self::CALENDAR_TYPE_YAHOO];
            $links = [];
            foreach($types as $type){
                $links[$type] = $url . self::getCalendarEndpoint($type, $registrantId);
            }

            return $links;
        }



    }

    private static function getCalendarEndpoint($type, $registrantId){
        $url = '';

        switch ($type){
            case 'google':
                $url .= 'calendar/google/add';
                break;
            case 'icalendar':
            case 'download':
                $url .= 'ics';
                break;
            case 'yahoo':
                $url .= 'cal';
                break;
        }

        $url .= '?user_id=' . $registrantId;

        if(self::CALENDAR_TYPE_DOWNLOAD !== $type){
            $url .= '&type=' . $type;
        }

        return $url;
    }

}
