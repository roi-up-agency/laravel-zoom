<?php
namespace RoiUp\Zoom\Http\Controllers;

use RoiUp\Zoom\Helpers\RegistrantLinks;
use RoiUp\Zoom\Models\Eloquent\Registrant;
use RoiUp\Zoom\Zoom;

class RegistrantController extends Controller
{
    private $registrant = null;

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

    private function execute($action){
        $data = RegistrantLinks::getData(request()->get('key'));

        $this->registrant = Registrant::whereMeetingId($data->meeting_id)->whereRegistrantId($data->registrant_id)->whereOccurrenceId($data->occurrence_id)->first();

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

                $response = $zoom->meeting->updateRegistrantStatus($data->meeting_id, $registrants, $action, $data->occurrence_id);
                return (isset($response['code']) && $response['code'] == 204) ? 204 : 500;
            }

            return 404;

        }else{
            return 404;
        }







    }
}
