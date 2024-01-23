<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Imageable prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix of the imageable actions.
    |
    */

    'prefix' => env('IMAGEABLE_PREFIX', 'api'),

    /*
    |--------------------------------------------------------------------------
    | Imageable authentication method
    |--------------------------------------------------------------------------
    |
    | This value determines the method of authentication for imageable actions.
    |
    */

    'auth-method' => env('APP_AUTH', 'auth.basic.once'),
];
