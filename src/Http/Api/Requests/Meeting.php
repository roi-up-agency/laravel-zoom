<?php
namespace RoiUp\Zoom\Http\Api\Requests;

use RoiUp\Zoom\Http\Api\Request;
use RoiUp\Zoom\Models\Eloquent\Occurrence;
use RoiUp\Zoom\Models\Zoom\Meeting as Model;
use RoiUp\Zoom\Models\Eloquent\Meeting as EloquentModel;
use RoiUp\Zoom\Models\Zoom\Registrant;
use RoiUp\Zoom\Models\Eloquent\Registrant as RegistrantModel;

class Meeting extends Request
{

    /**
     * List
     *
     * @param string $userId
     * @return array|mixed
     */
    public function getList($userId)
    {
        $response = $this->get("users/{$userId}/meetings");
        $meetings = [];
        foreach($response['meetings'] as $meeting){
            $meetings[] = Model::instantiate($meeting);
        }
        return $meetings;
    }

    /**
     * Create
     *
     * @param string $userId
     * @param array $data
     * @return array|mixed
     */
    public function create($userId, Model $meeting)
    {

        $meetingModel = new EloquentModel();
        $meetingModel->fillFromZoomModel($meeting, $userId);
        $meetingModel->status = 'creating';

        $meetingModel->save();

        $response =  $this->post("users/{$userId}/meetings", $meeting);

        if(isset($response['code']) && $response['code'] === 201){
            $meetingModel->uuid = $response['uuid'];
            $meetingModel->zoom_id = $response['id'];
            $meetingModel->join_url = $response['join_url'];
            $meetingModel->registration_url = !empty($response['registration_url']) ?  $response['registration_url'] : null;
            $meetingModel->start_url = !empty($response['start_url']) ?  $response['start_url'] : null;

            if(isset($response['occurrences'])){
                foreach ($response['occurrences'] as $occurrence){
                    $occurrenceModel = new Occurrence();
                    $occurrenceModel->meeting_id = $meetingModel->zoom_id;
                    $occurrenceModel->fill($occurrence);
                    $occurrenceModel->save();
                }
            }

            $meetingModel->save();

        }else{
            $meetingModel->delete();
        }

        return $response;
    }

    /**
     * Meeting
     *
     * @param string $meetingId
     * @return array|mixed
     */
    public function retrieve($meetingId)
    {
        return Model::instantiate($this->get("meetings/{$meetingId}"));
    }

    /**
     * Update
     *
     * @param string $meetingId
     * @param array $data
     * @return array|mixed
     */
    public function update($meetingId, Model $meeting)
    {
        $actualMeeting = EloquentModel::whereZoomId($meetingId)->first();

        $removeOccurrences = false;

        if($actualMeeting->recurrence !== json_encode($meeting->recurrence)){
            $removeOccurrences = true;
            foreach($actualMeeting->occurrences as $occurrence){
                if(sizeof($occurrence->approved) > 0){
                    return "There is some registrants approved for one ore more occurrences of this meeting";
                }
            }
        }

        return $this->patch("meetings/{$meetingId}", $meeting);

    }

    /**
     * Delete
     *
     * @param string $meetingId
     * @param string $ocurrenceId
     * @param string $scheduleForReminder
     * @return array|mixed
     */
    public function remove($meetingId, $ocurrenceId = null, $scheduleForReminder = true)
    {
        $query = '?schedule_for_reminder=' . $scheduleForReminder;

        if($ocurrenceId !== null){
            $query .= '&occurrence_id=' . $ocurrenceId;
        }

        return $this->delete("meetings/{$meetingId}{$query}");
    }

    /**
     * List Registrants
     *
     * @param string $meetingId
     * @param string $status
     * @return array|mixed
     */
    public function listRegistrants($meetingId)
    {
        $response = $this->get("meetings/{$meetingId}/registrants");
        $registrants = [];
        foreach($response['registrants'] as $registrant){
            $registrants[] = Registrant::instantiate($registrant);
        }
        return $registrants;
    }

    /**
     * Add Registrant
     *
     * @param string $meetingId
     * @param array $registrant
     * @return array|mixed
     */
    public function addRegistrant($meetingId, $registrant, $occurrenceId = null)
    {
        $allowed_fields = ["email", "first_name", "last_name", "address", "city", "country", "zip", "state", "phone", "industry", "org", "job_title", "purchasing_time_frame", "role_in_purchase_process", "no_of_employees", "comments", "custom_questions"];
        $reg = [];
        foreach($allowed_fields as $field){
            if($registrant->$field != ""){
                $reg[$field] = $registrant->$field;
            }
        }

        $queryString = '';
        if($occurrenceId !== null){
            $queryString .= '?occurrence_ids=' . $occurrenceId;
        }

        return $this->post("meetings/{$meetingId}/registrants". $queryString, $reg);
    }

    /**
     * Add Registrant
     *
     * @param string $meetingId
     * @param array $registrant
     * @param string $action
     * @return array|mixed
     */
    public function updateRegistrantStatus($meetingId, $registrants, $action, $occurrenceId = null)
    {
        $reges = [];
        foreach($registrants as $registrant){
            $reges[] = ['id' => $registrant->id, 'email' => $registrant->email];
        }

        $queryString = '';
        if($occurrenceId !== null){
            $queryString .= '?occurrence_id=' . $occurrenceId;
        }

        return $this->put("meetings/{$meetingId}/registrants/status" . $queryString , ['action' => $action, 'registrants' => $reges]);
    }

    // /**
    //  * Records
    //  *
    //  * @param string $meetingId
    //  * @return array|mixed
    //  */
    // public function records($meetingId)
    // {
    //     return $this->get("meetings/{$meetingId}/recordings");
    // }

}