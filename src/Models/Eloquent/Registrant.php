<?php

namespace Roiup\Zoom\Models\Eloquent;

class Registrant extends Model
{

    protected $table = 'zoom_registrants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id', 'registrant_id', 'email', 'first_name', 'last_name', 'join_url', 'status', 'create_time'
    ];

}
