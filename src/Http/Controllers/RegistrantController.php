<?php
namespace RoiUp\Zoom\Http\Controllers;

use RoiUp\Zoom\Helpers\RegistrantLinks;
use RoiUp\Zoom\Models\Eloquent\Registrant;
use RoiUp\Zoom\Zoom;

class RegistrantController extends Controller
{
    public function approve(){
        dd($this->execute('approve'));
    }

    public function deny(){
        dd($this->execute('deny'));
    }
    public function cancel(){
        dd($this->execute('cancel'));
    }

    private function execute($action){
        $data = RegistrantLinks::getData(request()->get('key'));

        $registrant = Registrant::whereMeetingId($data->meeting_id)->whereRegistrantId($data->registrant_id)->whereOccurrenceId($data->occurrence_id)->first();

        switch ($registrant->status){
            case 'pending':
                $zoom = app('Zoom');
                $registrants = [];
                $registrant = new \stdClass();
                $registrant->id = $data->registrant_id;
                $registrant->email = $data->registrant_email;
                $registrants[] = $registrant;

                return $zoom->meeting->updateRegistrantStatus($data->meeting_id, $registrants, $action, $data->occurrence_id);

                break;
            //TODO MORE CASES

        }

        return null;





    }
}
