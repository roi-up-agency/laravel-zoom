<?php 
namespace RoiUp\Zoom\Models\Zoom;

class Meeting extends Model
{

    const TYPE_INSTANT                   = 1;
    const TYPE_SCHEDULED                 = 2;
    const TYPE_RECURRENT_NO_FIXED_TIME   = 3;
    const TYPE_RECURRENT_FIXED_TIME      = 8;


    const SETTINGS_KEY_HOST_VIDEO        = 'host_video';
    const SETTINGS_KEY_PARTICIPANT_VIDEO = 'participant_video';
    const SETTINGS_KEY_JOIN_BEFORE_HOST  = 'join_before_host';
    const SETTINGS_KEY_MUTE_UPON_ENTRY   = 'mute_upon_entry';
    const SETTINGS_KEY_WATERMARK         = 'watermark';
    const SETTINGS_KEY_USE_PMI           = 'use_pmi';
    const SETTINGS_KEY_APPROVAL_TYPE     = 'approval_type';
    const SETTINGS_KEY_REGISTRATION_TYPE = 'registration_type';
    const SETTINGS_KEY_AUDIO             = 'audio';
    const SETTINGS_KEY_AUTO_RECORDING    = 'auto_recording';
    const SETTINGS_KEY_REGISTRANTS_EMAIL = 'registrants_email_notification';

    const SETTINGS_APPROVAL_TYPE_AUTO                = 0;
    const SETTINGS_APPROVAL_TYPE_MANUAL              = 1;
    const SETTINGS_APPROVAL_TYPE_NO_REGISTRATION_REQ = 2;

    const SETTINGS_REGISTRATION_TYPE_ONCE_ALL_OCCURRENCES    = 1;
    const SETTINGS_REGISTRATION_TYPE_ONCE_ONE_OCCURRENCES    = 2;
    const SETTINGS_REGISTRATION_TYPE_ONCE_MANY_OCCURRENCES   = 3;

    const SETTINGS_AUDIO_BOTH        = 'both';
    const SETTINGS_AUDIO_TELEPHONY   = 'telephony';
    const SETTINGS_AUDIO_VOIP        = 'voip';

    const SETTINGS_AUTO_RECORDING_LOCAL  = 'local';
    const SETTINGS_AUTO_RECORDING_CLOUD  = 'cloud';
    const SETTINGS_AUTO_RECORDING_NONE   = 'none';

    const SETTINGS_REGISTRANTS_EMAIL     = '';


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
            "registrants_email_notification" => '', // strin
        ],
    ];

}