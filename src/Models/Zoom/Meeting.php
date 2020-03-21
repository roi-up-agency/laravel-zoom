<?php 
namespace RoiUp\Zoom\Models\Zoom;

class Meeting extends Model
{

    public const TYPE_INSTANT                   = 1;
    public const TYPE_SCHEDULED                 = 2;
    public const TYPE_RECURRENT_NO_FIXED_TIME   = 3;
    public const TYPE_RECURRENT_FIXED_TIME      = 8;

    public const SETTINGS_APPROVAL_TYPE_AUTO                = 0;
    public const SETTINGS_APPROVAL_TYPE_MANUAL              = 1;
    public const SETTINGS_APPROVAL_TYPE_NO_REGISTRATION_REQ = 2;

    public const SETTINGS_REGISTRATION_TYPE_ONCE_ALL_OCCURRENCES    = 1;
    public const SETTINGS_REGISTRATION_TYPE_ONCE_ONE_OCCURRENCES    = 2;
    public const SETTINGS_REGISTRATION_TYPE_ONCE_MANY_OCCURRENCES   = 3;

    public const SETTINGS_AUDIO_BOTH        = 'both';
    public const SETTINGS_AUDIO_TELEPHONY   = 'telephony';
    public const SETTINGS_AUDIO_VOIP        = 'voip';

    public const SETTINGS_AUTO_RECORDING_LOCAL  = 'local';
    public const SETTINGS_AUTO_RECORDING_CLOUD  = 'cloud';
    public const SETTINGS_AUTO_RECORDING_NONE   = 'none';


	protected $attributes = [
        'uuid' => '',
        "id" => '', // string
        "host_id" => '', // string
        "created_at" => '', // string [date-time]
        "join_url" => '', // string
        "topic" => '', // string
        "type" => '', // integer
        "start_time" => '', // string [date-time]
        "duration" => '', // integer
        "timezone" => '', // string
        "password" => '', // string
        "agenda" => '', // string
        "recurrence" => [],
        "occurrences" => [],
        "settings" => [
            "host_video" => '', // boolean
            "participant_video" => '', // boolean
            "cn_meeting" => '', // boolean
            "in_meeting" => '', // boolean
            "join_before_host" => '', // boolean
            "mute_upon_entry" => '', // boolean
            "watermark" => '', // boolean
            "use_pmi" => '', // boolean
            "approval_type" => '', // integer
            "registration_type" => '', // integer
            "audio" => '', // string
            "auto_recording" => '', // string
            "enforce_login" => '', // boolean
            "enforce_login_domains" => '', // string
            "alternative_hosts" => '', // strin
        ],
    ];

}