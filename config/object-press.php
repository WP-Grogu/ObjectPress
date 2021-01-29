<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache options
    |--------------------------------------------------------------------------
    |
    | Insert here your cache configurations
    |
    */
    'cache' => [
        'driver' => 'Files',
        'path'   => wp_upload_dir()['basedir'] . '/../cache',
    ],
];
