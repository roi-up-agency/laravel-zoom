<?php

namespace RoiUp\Zoom\Events\Notifications;

use RoiUp\Zoom\Models\Eloquent\Registrant;

class SendRegistrantConfirm
{

    public $registrant;

    public function __construct(Registrant $registrant)
    {
        $this->registrant = $registrant;
    }
}