<?php

namespace Roiup\Zoom\Models\Eloquent;


class Meeting extends Model
{

    protected $table = 'zoom_meetings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'zoom_id', 'host_id', 'topic', 'join_url', 'type', 'start_time', 'duration', 'timezone', 'agenda', 'password', 'recurrence', 'settings'
    ];

}
