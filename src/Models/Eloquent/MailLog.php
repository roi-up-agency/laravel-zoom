<?php

namespace RoiUp\Zoom\Models\Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

class MailLog extends Eloquent
{

    protected $table = 'zoom_mails_log';

    public static $REGISTRANT_CANCELLED     = 'registrant.cancelled';
    public static $REGISTRANT_APPROVED      = 'registrant.approved';
    public static $REGISTRANT_DENIED        = 'registrant.denied';
    public static $REGISTRANT_NEW           = 'registrant.new';
    public static $REGISTRATION_DELETED     = 'registration.deleted';
    public static $REGISTRATION_CONFIRM     = 'registration.confirm';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registrant_id', 'meeting_id', 'occurrence_id', 'action', 'subject', 'sendee', 'data'
    ];
}
