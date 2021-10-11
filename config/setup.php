<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App custom post types declaration
    |--------------------------------------------------------------------------
    |
    | Insert here you app/theme custom post types
    | Format : Path\To\CustomPostType\ClassName::class
    |
    */
    'cpts' => [
        // App\Wordpress\PostTypes\Article::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App taxonomies declaration
    |--------------------------------------------------------------------------
    |
    | Insert here you app/theme taxonomies
    | Format : Path\To\Taxonomy\ClassName::class
    |
    */
    'taxonomies' => [
        // App\Wordpress\Taxonomies\Category::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App APIs declaration
    |--------------------------------------------------------------------------
    |
    | Insert here you app/theme api routes
    | Format : Path\To\Api\ClassName::class
    |
    */
    'apis' => [
        // App\Api\GetCategories::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App Hooks declaration
    |--------------------------------------------------------------------------
    |
    | This is the place you declare your app hooks & filters.
    | Based on https://github.com/AmphiBee/hooks package.
    |
    */
    'hooks' => [
        'actions' => [
            OP\Core\Hooks\SetupWordpress::class,
            OP\Core\Hooks\SetupCli::class,
            OP\Core\Hooks\SetupApi::class,
            OP\Core\Hooks\SetupGraphql::class,
        ],
        'filters' => [
            //
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | App User roles declaration
    |--------------------------------------------------------------------------
    |
    | Insert here your user roles
    | Format: Path\To\User\Role\ClassName::class
    |
    */
    'user-roles' => [
        // App\Wordpress\Roles\Administrator::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App CLI Commands
    |--------------------------------------------------------------------------
    |
    | Insert here your WP-CLI commands.
    |
    */
    'commands' => [
        // App\Wordpress\Commands\UpdateUsers::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App Models declaration
    |--------------------------------------------------------------------------
    |
    | Insert here your app Models.
    | Please note that declaring model is not needed if you're respecting the naming convention.
    |
    | @doc http://docs.objectpress.hydrat.agency/#/the-basics/models
    |
    | @format [ 'custom-post-type-name' => Namespace\Models\MyCustomPostType::class ]
    |
    */
    'models' => [
        // 'my-invoice' => App\Models\Invoice::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | App GraphQL types declaration
    |--------------------------------------------------------------------------
    |
    | Insert here your GraphQL Types
    | Format: Path\To\GQL\Type\ClassName::class
    |
    */
    'gql-types' => [
        // App\Graphql\Types\MediaGallery,
    ],


    /*
    |--------------------------------------------------------------------------
    | App GraphQL fields declaration
    |--------------------------------------------------------------------------
    |
    | Insert here your GraphQL Fields
    | Format: Path\To\GQL\Field\ClassName::class
    |
    */
    'gql-fields' => [
        // App\Graphql\Fields\MediaGallery,
    ],
];
