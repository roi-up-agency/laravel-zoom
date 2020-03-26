<?php

namespace RoiUp\Zoom\EventActions\Meeting;

class Registrant {
    const CREATED    = 'meeting.registration_created';
    const APPROVED   = 'meeting.registration_approved';
    const CANCELLED  = 'meeting.registration_cancelled';
    const DENIED     = 'meeting.registration_denied';
}