<?php

namespace RoiUp\Zoom\Models\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ModelLog extends Eloquent
{

    protected $table = 'zoom_models_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_log_id', 'model', 'model_id','action', 'attributes', 'changes'
    ];
}
