To create a taxonomy, you first declare your taxonomy properties in the `app/Taxonomies` folder, then initiate it thru the wordpress `function.php` file.  
 
## 1. Declare your Taxonomy properties :

```php
namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
    protected static $domain;

    protected static $taxonomy = 'example-taxonomy';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     */
    public static $singular = 'Example Taxonomy';
    public static $plural   = 'Example Taxonomies';


    /**
     * Enable graphql
     *
     * @var bool
     */
    public static $graphql_enabled = false;


    /**
     * Post types that will have the taxonomy
     *
     * @var array
     */
    protected static $post_types = [
        'post',
        'example',
    ];


    /**
     * Taxonomy argument to override over boilerplate
     */
    public static $args_override = [];


    /**
     * Taxonomy labels to override over boilerplate
     */
    public static $labels_override = [];
}

```

You can override post type `$args` or `$labels` as you please inside the `$args_override`/`$labels_override` vars. Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_taxonomy/) to have the listing of available arguments.  



## 2. Initiate your Taxonomy : 

Inside your `function.php` file :  

```php
<?php

/**
 * Theme configuration class
 */
$theme = OP\Framework\Theme::getInstance();


/**
 * Post types & taxonomies initialisation
 */
$theme->on('init', function () {
    // Register CPTs
    ...

    // Register Taxonomies
    new App\Taxonomies\ExampleTaxonomy('148-cpts');
});
```