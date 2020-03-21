<?php

namespace RoiUp\Zoom\Events;

use RoiUp\Zoom\EventActions\Meeting\Meeting;
use RoiUp\Zoom\EventActions\Meeting\Registrant;

class EventsMapping{

    public static $MAP = [
        Meeting::CREATED        => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingCreated',
        Meeting::UPDATED        => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingUpdated',
        Meeting::DELETED        => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingDeleted',
        Meeting::STARTED        => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingStarted',
        Meeting::ENDED          => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingEnded',
        Registrant::CREATED     => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingRegistrantCreated',
        Registrant::APPROVED    => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingRegistrantApproved',
        Registrant::CANCELLED   => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingRegistrantCancelled',
        Registrant::DENIED      => 'RoiUp\\Zoom\\Events\\Meeting\\MeetingRegistrantDenied',
    ];
}
