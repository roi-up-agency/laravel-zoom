<?php

namespace RoiUp\Zoom\Models\Eloquent;

use RoiUp\Zoom\Models\Zoom\Registrant as ZoomModel;
class Registrant extends Model
{

    protected $table = 'zoom_registrants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id', 'occurrence_id', 'registrant_id', 'email', 'first_name', 'last_name', 'join_url', 'status', 'create_time'
    ];

    public function fillFromZoomModel(ZoomModel $registrant, $meetingId, $occurrenceId){
        $this->meeting_id       = $meetingId;
        $this->occurrence_id    = $occurrenceId;

        foreach($this->getFillable() as $value){
            $field = $value == 'registrant_id' ? 'id' : $value;

            $registrantValue = $registrant->$field;

            if(!empty($registrantValue)){
                $this->$value =  $registrantValue;
            }

        }
    }

    public function occurrence(){
        return $this->belongsTo(Occurrence::class, 'occurrence_id', 'occurrence_id');
    }

    public function meeting(){
        return $this->belongsTo(Meeting::class, 'meeting_id', 'zoom_id');
    }

    public function fullName(){
        return $this->first_name . ' ' . $this->last_name;
    }
    
    public function isSubscribed(){
        return Registrant::whereMeetingId($this->meeting_id)->whereRegistrantId($this->registrant_id)->whereOccurrenceId($this->occurrence_id)->count() > 0;
    }
}
