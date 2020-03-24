<?php

return [

    'api_key' => env('ZOOM_KEY'),
    'api_secret' => env('ZOOM_SECRET'),
    'events_token' => env('ZOOM_EVENTS_TOKEN'),
    'base_url'          => env('ZOOM_BASE_API_URL', 'https://api.zoom.us/v2/'),
    'links_base_url'    => env('ZOOM_LINKS_BASE_URL', 'https://zoom.us/'),
];
