<?php
//Contains configurations for this application

return [

    /*
    |--------------------------------------------------------------------------
    | Database Host
    |--------------------------------------------------------------------------
    |
    | This value is the host name or IP address of your Database.
    |
     */
    'db_host' => '127.0.0.1',

    /*
    |--------------------------------------------------------------------------
    | Database Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your Database.
    |
     */
    'db_name' => 'mvc',

    /*
    |--------------------------------------------------------------------------
    | Database Username
    |--------------------------------------------------------------------------
    |
    | This value is the username of your Database.
    |
     */
    'db_username' => 'root',

    /*
    |--------------------------------------------------------------------------
    | Database Password
    |--------------------------------------------------------------------------
    |
    | This value is the password of your Database.
    |
     */
    'db_password' => '',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | This value is the default timezone which will be set for PHP for this Application.
    |
     */
    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Throttle Request Flag
    |--------------------------------------------------------------------------
    |
    | This value enables/disables request throttling feature.
    | This feature prevents requests to exceed a certain value (request_throttle_count) per second per session.
    |
     */
    'enable_request_throttle' => true,

    /*
    |--------------------------------------------------------------------------
    | Throttle Request Cound
    |--------------------------------------------------------------------------
    |
    | This value is the count of requests that are to be allowed per second per session.
    |
     */
    'request_throttle_count' => 10,
];
