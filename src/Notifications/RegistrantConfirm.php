<?php

namespace RoiUp\Zoom\Notifications;


use Illuminate\Mail\Mailable;

class RegistrantConfirm extends Mailable
{


    /**
     * The order instance.
     *
     * @var Order
     */
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('zoom::emails.registration-confirm')
            ->with($this->data)->subject(trans('zoom::emails.registration_thanks_subject'));
    }

}