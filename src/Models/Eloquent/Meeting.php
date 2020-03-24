<?php

namespace RoiUp\Zoom\Models\Eloquent;

use RoiUp\Zoom\Models\Zoom\Meeting as ZoomMeeting;

class Meeting extends Model
{

    protected $table = 'zoom_meetings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'zoom_id', 'host_id', 'topic', 'join_url', 'type', 'start_time', 'duration', 'timezone', 'agenda', 'password', 'recurrence', 'settings', 'status'
    ];

    public function occurrences(){
        return $this->hasMany(Occurrence::class, 'meeting_id', 'zoom_id');
    }

    public function host(){
        return $this->hasOne(Host::class, 'host_id', 'host_id');
    }


    public function fillFromZoomModel(ZoomMeeting $meeting, $hostId){

        $this->host_id = $hostId;
        if(sizeof($meeting) == 1){
            $meeting = $meeting->returned();
        }

        foreach($meeting as $key => $value){

            switch ($key){
                case 'settings':
                case 'recurrence':
                    $value = json_encode($value);
                    break;
            }

            if($key !== 'created_at'){
                $this->$key =  $value;
            }

        }

    }
}
