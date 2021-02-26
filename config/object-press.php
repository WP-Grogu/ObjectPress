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
    | Wordpress configurations
    |--------------------------------------------------------------------------
    |
    | Insert here your Wordpress configurations.
    |
    */
    'wp' => [
        // The wordpress theme template file name structure.
        // %s represents the template name. Used to find pages from their template identifier.
        'template-files-structure' => 'template-%s.php',
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
