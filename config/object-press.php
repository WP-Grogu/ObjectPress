<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application(s) containers.
    |--------------------------------------------------------------------------
    |
    | Add your app/frameworks container here to support them with ObjectPress.
    |
    | @see https://php-di.org/doc/container-configuration.html
    | @see https://github.com/AcclimateContainer/acclimate-container
    |
    */
    'containers' => [
        //
    ],


    /*
    |--------------------------------------------------------------------------
    | Cache options
    |--------------------------------------------------------------------------
    |
    | Insert here your cache configurations
    |
    */
    'cache' => [
        'active' => false,
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
        'flex-thumb-relative-path' => '',
    ],


    /*
    |--------------------------------------------------------------------------
    | Theme options
    |--------------------------------------------------------------------------
    |
    | Insert here your theme/app configurations so ObjectPress can handle your theme.
    |
    */
    'theme' => [
        'psr-prefix' => 'App',
    ],


    /*
    |--------------------------------------------------------------------------
    | View configurations
    |--------------------------------------------------------------------------
    |
    | Manage here the view paths and options for the blade compiler engine.
    |
    */
    'view' => [

        'paths' => [
            'inputs' => [
                get_stylesheet_directory() . '/resources/views',
            ],
            
            'output' => wp_upload_dir()['basedir'] . '/cache/blade',

            'composers' => [
                get_stylesheet_directory() . '/app/View/Composers'
            ],
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Database options
    |--------------------------------------------------------------------------
    |
    | Manage here your database options.
    | ObjectPress supports the Eloquent ORM.
    |
    */
    'database' => [
        /**
         * Should we apply a filter (scope) to all your Eloquent queries,
         * to only get items in the current language ?
         */
        'global_scope_language' => false,
    ],

];
