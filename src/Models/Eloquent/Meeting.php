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

    public function ifManualApproveNeeded(){
        return json_decode($this->settings)->approval_type === \RoiUp\Zoom\Models\Zoom\Meeting::SETTINGS_APPROVAL_TYPE_MANUAL;
    }

    public function mustBeRegisteredInAllOccurrences(){
        return json_decode($this->settings)->registration_type === \RoiUp\Zoom\Models\Zoom\Meeting::SETTINGS_REGISTRATION_TYPE_ONCE_ALL_OCCURRENCES;
    }

    public function getSetting($setting){
        return json_decode($this->settings)->$setting;
    }

    public function registrantOccurrences($registrantId){
        $registrants = Registrant::whereMeetingId($this->zoom_id)->whereRegistrantId($registrantId)->get();
        $occurrences = [];
        foreach ($registrants as $registrant){
            $occurrences[] = Occurrence::whereOccurrenceId($registrant->occurrence_id)->whereMeetingId($this->zoom_id)->first();
        }

        return $occurrences;
    }

    public function fillFromZoomModel(ZoomMeeting $meeting, $hostId){

        $meeting = $meeting->returned();

        foreach($meeting as $key => $value){

            if(!in_array($key, $this->fillable)){
                continue;
            }

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

        $this->host_id = $hostId;

    }

    public function mergeSettings($settings){
        $actualSettingsArray = json_decode($this->settings);
        foreach ($settings as $key => $value){
            if(isset($actualSettingsArray->$key)){
                $actualSettingsArray->$key = $value;
            }
        }
        $this->settings = json_encode($actualSettingsArray);
    }

    public function updateOccurrences($occurrences){
        Occurrence::whereMeetingId($this->zoom_id)->delete();
        Registrant::whereMeetingId($this->zoom_id)->delete();

        if(sizeof($occurrences) > 0){

            foreach ($occurrences as $occurrence){
                $occurrenceModel = new Occurrence();
                $occurrenceModel->meeting_id = $this->zoom_id;
                $occurrenceModel->fill($occurrence);
                $occurrenceModel->status = 'available';
                $occurrenceModel->save();
            }
        }
    }
}
