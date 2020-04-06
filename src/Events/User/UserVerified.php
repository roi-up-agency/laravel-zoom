<?php

namespace RoiUp\Zoom\Events\User;

use RoiUp\Zoom\Events\ZoomEvent;
use RoiUp\Zoom\Models\Eloquent\Host;

class UserVerified
{
    public $user;

    public function __construct(Host $host)
    {
        $this->user = $host;
    }
}