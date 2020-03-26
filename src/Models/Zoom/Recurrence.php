<?php 
namespace RoiUp\Zoom\Models\Zoom;

class Recurrence extends Model
{

    const TYPE_DAILY         = 1;
    const TYPE_WEEKLY        = 2;
    const TYPE_MONTHLY       = 3;

    const SUNDAY     = 1;
    const MONDAY     = 2;
    const TUESDAY    = 3;
    const WEDNESDAY  = 4;
    const THURSDAY   = 5;
    const FRIDAY     = 6;
    const SATURDAY   = 7;

    const LAST_WEEK      = -1;
    const FIRST_WEEK     = 1;
    const SECOND_WEEK    = 2;
    const THIRD_WEEK     = 3;
    const FOURTH_WEEK    = 4;


	protected $attributes = [
        "type" => '', // integer
        "repeat_interval" => '', // integer
        "weekly_days" => '', // integer
        "monthly_day" => '', // integer
        "monthly_week" => '', // integer
        "monthly_week_day" => '', // integer
        "end_times" => '', // integer
        "end_date_time" => '', // string [date-time]
    ];

}