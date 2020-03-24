<?php

namespace RoiUp\Zoom\Models\Eloquent;

class Occurrence extends Model
{

    protected $table = 'zoom_occurrences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id', 'occurrence_id', 'start_time', 'status', 'duration'
    ];

    public function registrants(){
        return $this->hasMany(Registrant::class, 'occurrence_id', 'occurrence_id');
    }

    public function approved(){
        return $this->hasMany(Registrant::class, 'occurrence_id', 'occurrence_id')->where('status', 'approved');
    }

    public function denied(){
        return $this->hasMany(Registrant::class, 'occurrence_id', 'occurrence_id')->where('status', 'denied');
    }

    public function meeting(){
        return $this->belongsTo(Meeting::class, 'meeting_id', 'zoom_id');
    }
}
