<?php
namespace RoiUp\Zoom\Http\Controllers;

use RoiUp\Zoom\Helpers\RegistrantLinks;
use RoiUp\Zoom\Models\Eloquent\Registrant;
use RoiUp\Zoom\Models\Zoom\Registrant as ZoomRegistrant;
use RoiUp\Zoom\Zoom;

class RegistrantController extends Controller
{
    private $registrant = null;
    
    public function add(){
        $data = request()->all();

        unset($data['token']);

        $meetingId = $data['meetingId'];
        unset($data['meetingId']);

        $occurrenceId = null;
        if(!empty($data['occurrenceId'])){
            $occurrenceId = $data['occurrenceId'];
            unset($data['occurrenceId']);
        }

        $registrant = ZoomRegistrant::instantiate($data);

        $response = app('Zoom')->meeting->addRegistrant($meetingId, $registrant, $occurrenceId);

        if(isset($response['code']) && $response['code'] == 201){
            return response()->json(['status' => 'ok'], 200);
        }else{
            return response()->json(['status' => 'ko'], 200);
        }
    }
    
    public function approve(){
        $response = $this->execute('approve');
        if($response == 204){
            return view('zoom::registration.approve-success', ['registrant' => $this->registrant, 'meeting' => $this->registrant->meeting, 'occurrence' => $this->registrant->occurrence, 'host' => $this->registrant->meeting->host]);
        }else{
            abort($response);
        }
    }

    public function deny(){
        $response = $this->execute('deny');
        if($response == 204){
            return view('zoom::registration.deny-success', ['registrant' => $this->registrant, 'meeting' => $this->registrant->meeting, 'occurrence' => $this->registrant->occurrence, 'host' => $this->registrant->meeting->host]);
        }else{
            abort($response);
        }
    }
    public function cancel(){
        $response = $this->execute('cancel');
        if($response == 204){
            return view('zoom::registration.cancel-success', ['registrant' => $this->registrant, 'meeting' => $this->registrant->meeting, 'occurrence' => $this->registrant->occurrence, 'host' => $this->registrant->meeting->host]);
        }else{
            abort($response);
        }
    }

    private function execute($action)
    {
        $data = RegistrantLinks::getData(request()->get('key'));

        if (isset($data->occurrence_id)){
            $this->registrant = Registrant::whereMeetingId($data->meeting_id)->whereRegistrantId($data->registrant_id)->whereOccurrenceId($data->occurrence_id)->first();
        }else{
            $this->registrant = Registrant::whereMeetingId($data->meeting_id)->whereRegistrantId($data->registrant_id)->first();
        }

        if($this->registrant !== null) {


            $shouldExecute = false;
            if ($action == 'cancel' || $this->registrant->status === 'pending') {
                $shouldExecute = true;
            }

           if ($shouldExecute) {
                $zoom = app('Zoom');
                $registrants = [];
                $registrant = new \stdClass();
                $registrant->id = $data->registrant_id;
                $registrant->email = $data->registrant_email;
                $registrants[] = $registrant;

                if(isset($data->occurrence_id)){
                    $response = $zoom->meeting->updateRegistrantStatus($data->meeting_id, $registrants, $action, $data->occurrence_id);
                }else{
                    $response = $zoom->meeting->updateRegistrantStatus($data->meeting_id, $registrants, $action);
                    dd($response);
                }

                return (isset($response['code']) && $response['code'] == 204) ? 204 : 500;
            }

            return 404;

        }else{
            return 404;
        }







    }
}
