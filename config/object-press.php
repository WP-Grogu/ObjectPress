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


    /*
    |--------------------------------------------------------------------------
    | ACF options (thirdparty)
    |--------------------------------------------------------------------------
    |
    | Insert here your ACF plugin configuration.
    |
    */
    'acf' => [
        // Paths to export files, Json or PHP (if you're using ACF extended)
        'json-path' => get_template_directory() . '/acf-json',
        'php-path'  => get_template_directory() . '/acf-php',

        // Path to flexible content thumbnails directory.
        // Relative path from theme folder root (get_template_directory()).
        'flex-thumb-relative-path' => '/static/flexible_thumbnails',
    ],

];
