# Custom post types

Taxonomies are Wordpesss posts categories.
With ObjectPress, you want to optimise this taxonomy creation, to gain time and easily enable/disable taxonomies in your theme.

This class is inspired by [generate wordpress](https://generatewp.com/taxonomy/) style, you can checkup `labels` and `args` overwritable on their website. 
 
## Defining your taxonomy properties

Define your taxonomies inside the `app/Taxonomies` folder. You can start with the `Minimal` template, and add needed properties displayed in the `Full` tab (displayed values are defaults).


<!-- tabs:start -->

#### ** Minimal **

```php
namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
    /**
     * i18n string translation domain
     *
     * @var string
     */
    protected static $domain = 'theme-taxos';

    /**
     * Taxonomy identifier
     *
     * @var string
     */
    protected static $taxonomy = 'custom-taxonomy';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public static $singular = 'Custom Taxonomy';
    public static $plural   = 'Custom Taxonomies';

    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     */
    protected static $post_types = [
        'Post',
    ];
}
```


#### ** Full **

```php
namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
    /**
     * i18n string translation domain
     *
     * @var string
     */
    protected static $domain = 'theme-taxos';

    /**
     * Taxonomy identifier
     *
     * @var string
     */
    protected static $taxonomy = 'custom-taxonomy';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public static $singular = 'Custom Taxonomy';
    public static $plural   = 'Custom Taxonomies';

    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     */
    protected static $post_types = [];


    /**
     * Enable graphql on this taxonomy
     *
     * @var bool
     */
    public static $graphql_enabled = false;


    /**
     * Taxonomy argument to overide over boilerplate
     *
     * @var array
     */
    public static $args_override = [];
    

    /**
     * Taxonomy labels to overide over boilerplate
     *
     * @var array
     */
    public static $labels_override = [];
}
```

<!-- tabs:end -->

You can override post type `args` or `labels` as you please inside their dedicated vars `$args_override/$labels_override`.  

> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_taxonomy/) or the [generate WP website](https://generatewp.com/taxonomy/) to have a listing of available arguments. 




## Initiate your Taxonomy 

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
    App\Taxonomies\ExampleTaxonomy::init()
});
```