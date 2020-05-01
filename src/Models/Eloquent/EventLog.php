<?php

namespace RoiUp\Zoom\Models\Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

class EventLog extends Eloquent
{

    protected $table = 'zoom_events_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event', 'object_id', 'host_id', 'operator', 'object_data', 'payload', 'error_trace', 'status'
    ];
}
