<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Forge API V2 Token
    |--------------------------------------------------------------------------
    |
    | Your Laravel Forge API token is used to authenticate all requests to the
    | Forge API. You can generate a new API token from your Forge account
    | settings at https://forge.laravel.com/profile/api
    |
    */

    'token' => env('FORGE_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout value (in seconds) for HTTP requests made to the Forge API.
    | This defines how long the client will wait for a response before timing
    | out. The default is 90 seconds.
    |
    */

    'timeout' => env('FORGE_TIMEOUT', 90),

    /*
    |--------------------------------------------------------------------------
    | Organisation ID
    |--------------------------------------------------------------------------
    |
    | If you are managing servers under a specific organisation in Laravel
    | Forge, you can specify the organisation ID here. This will scope all
    | API requests to that organisation's resources.
    |
    */

    'organisation' => env('FORGE_ORGANISATION'),
];
