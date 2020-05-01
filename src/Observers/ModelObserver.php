<?php

namespace RoiUp\Zoom\Observers;

use RoiUp\Zoom\Models\Eloquent\EventLog;
use RoiUp\Zoom\Models\Eloquent\Model;
use RoiUp\Zoom\Models\Eloquent\ModelLog;

class ModelObserver
{
    private function getModel(Model $model){

        $log = new ModelLog();
        //'event_log_id', 'model', 'model_id','action', 'attributes', 'changes'
        $log->model = $model->getTable();
        $log->model_id = $model->getKey();
        $log->attributes = json_encode($model->attributesToArray());
        $log->event_log_id = $this->getEventLog();
        return $log;
    }
    /**
     * Listen to the User created event.
     *
     * @param  $user
     * @return void
     */
    public function created(Model $model)
    {
        $log = $this->getModel($model);
        $log->action = 'create';
        $log->save();
    }

    public function updated(Model $model)
    {

        $log = $this->getModel($model);
        $log->action = 'update';

        $original = $model->getOriginal();
        $changes = [];

        foreach ($model->getDirty() as $key => $value) {
            if($key !== 'updated_at' && $key !== 'created_at'){
                $changes[$key] = [
                    'original' => $original[$key],
                    'changes' => $value,
                ];
            }

        }

        $log->changes = json_encode($changes);

        $log->save();
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  $model
     * @return void
     */
    public function deleted($model)
    {
        $log = $this->getModel($model);
        $log->action = 'delete';
        $log->save();

    }

    private function getEventLog(){
        $eventLog = EventLog::whereStatus('logging')->orderBy('id', 'desc')->first();

        return !empty($eventLog) ? $eventLog->id : null;
    }
}

