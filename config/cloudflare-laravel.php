<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | This is the Auth vars.
    |
    */

    'driver' => env('CLOUDFLARE_DRIVER', 'api'),
    'token' => env('CLOUDFLARE_TOKEN', ''),
    'email' => env('CLOUDFLARE_EMAIL', ''),

];
