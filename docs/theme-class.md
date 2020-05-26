```php
<?php

/**
 * This file manage the theme configurations
 */

if (!session_id()) {
    session_start();
}

/**
 * Theme configuration class
 */
$theme = OP\Framework\Theme::getInstance();


/**
 * Post types & taxonomies initialisation
 */
$theme->on('init', function () {
    // Register CPTs
    new App\CustomPostTypes\Gallery('148-cpts');
    new App\CustomPostTypes\City('148-cpts');
    
    // Register Taxonomies
    new App\Taxonomies\Type('148-cpts');
});


/**
 * API routes initialisation
 */
$theme->on('rest_api_init', function () {
    // Register APIs
    App\Api\GalleryApi::init();
    App\Api\CityApi::init();
});


/**
 * GraphQL Types & fields initialisation
 */
$theme->on('graphql_register_types', function () {
    // Register GraphQL Types
    App\GraphQL\Types\GalleryItem::register();
    App\GraphQL\Types\GalleryFilter::register();
    
    // Register GraphQL Fields
    App\GraphQL\Fields\AverageColor::register();
    App\GraphQL\Fields\GalleryItems::register();
    App\GraphQL\Fields\GalleryFilters::register();
});


/**
 * Register nav menus
 */
$theme->addNavMenus([
    'main' => 'Main Menu',
    'footer' => 'Footer Menu',
]);


/**
 * Push headers
 */
$theme->on('rest_api_init', function () {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        return $value;
    });
}, 15);
```