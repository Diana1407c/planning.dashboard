<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | This will be used for Symmetric algorithms only (HMAC),
    | since RSA and ECDSA use a private/public key combo (See below).
    |
    */

    'secret' => env('JWT_SECRET', 'secret'),

    'time' => env('JWT_TIME', 60),

    'headers' => [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ]
];
