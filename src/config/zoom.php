<?php

return [

    'api_key' => env('ZOOM_KEY'),
    'api_secret' => env('ZOOM_SECRET'),
    'events_token' => env('ZOOM_EVENTS_TOKEN'),
    'base_url'          => env('ZOOM_BASE_API_URL', 'https://api.zoom.us/v2/'),
    'links_base_url'    => env('ZOOM_LINKS_BASE_URL', 'https://zoom.us/'),
    'emails_date_format'    => env('ZOOM_EMAILS_DATE_FORMAT', 'Y-m-d H:i:s'),
    'emails_logo_url'    => env('ZOOM_EMAILS_LOGO_URL'),
];
