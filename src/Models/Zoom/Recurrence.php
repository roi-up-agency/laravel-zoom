<?php 
namespace RoiUp\Zoom\Models\Zoom;

class Recurrence extends Model
{

    public const TYPE_DAILY         = 1;
    public const TYPE_WEEKLY        = 2;
    public const TYPE_MONTHLY       = 3;

    public const SUNDAY     = 1;
    public const MONDAY     = 2;
    public const TUESDAY    = 3;
    public const WEDNESDAY  = 4;
    public const THURSDAY   = 5;
    public const FRIDAY     = 6;
    public const SATURDAY   = 7;

    public const LAST_WEEK      = -1;
    public const FIRST_WEEK     = 1;
    public const SECOND_WEEK    = 2;
    public const THIRD_WEEK     = 3;
    public const FOURTH_WEEK    = 4;


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