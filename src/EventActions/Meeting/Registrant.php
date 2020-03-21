<?php

namespace RoiUp\Zoom\EventActions\Meeting;

class Registrant {
    public const CREATED    = 'meeting.registration_created';
    public const APPROVED   = 'meeting.registration_approved';
    public const CANCELLED  = 'meeting.registration_cancelled';
    public const DENIED     = 'meeting.registration_denied';
}