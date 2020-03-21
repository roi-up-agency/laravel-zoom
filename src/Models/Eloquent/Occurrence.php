<?php

namespace Roiup\Zoom\Models\Eloquent;

class Occurrence extends Model
{

    protected $table = 'zoom_occurrences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id', 'occurrence_id', 'start_time', 'status', 'duration'
    ];

}
