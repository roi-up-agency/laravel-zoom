<?php

namespace RoiUp\Zoom\Models\Eloquent;

use RoiUp\Zoom\Models\Zoom\Meeting as ZoomMeeting;

class Host extends Model
{

    protected $table = 'zoom_hosts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'host_id', 'first_name', 'last_name', 'email'
    ];

    public function meetings(){
        return $this->hasMany(Meeting::class, 'host_id', 'host_id');
    }
}
