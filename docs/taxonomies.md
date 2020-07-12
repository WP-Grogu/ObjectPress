# Custom post types

Taxonomies are Wordpesss posts categories.
With ObjectPress, you want to optimise this taxonomy creation, to gain time and easily enable/disable taxonomies in your theme.

This class is inspired by [generate wordpress](https://generatewp.com/taxonomy/) style, you can checkup overwritable `labels` and `args` on their website. 
 
## Defining your taxonomy properties

Define your taxonomies inside the `app/Taxonomies` folder. You can start with the `Minimal` template, and add needed properties displayed in the `Full` tab (displayed values are defaults).


<!-- tabs:start -->

#### ** Minimal **

```php
<?php
namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
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
<?php
namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
    /**
     * Taxonomy name
     *
     * @var string
     * @since 0.1
     */
    protected static $taxonomy = 'example-taxonomy';


    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     * @since 0.1
     */
    public static $singular = 'Example Taxonomy';
    public static $plural   = 'Example Taxonomies';


    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     * @since 0.1
     */
    protected static $post_types = [];


    /**
     * Activate 'single term' mode on this taxonomy
     *
     * @var bool
     * @since 1.3
     */
    public static $single_term = false;


    /**
     * 'single term' mode params 
     * 
     * @var array
     * @since 1.3
     */
    public static $single_term_params = [
        'default_term' => 'my-category',
    ];

    /**
     * CPT/Taxonomy argument to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $args_override = [];


    /**
     * CPT/Taxonomy labels to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $labels_override = [];


    /**
     * Enable graphql on this CPT/Taxonomy
     *
     * @var bool
     * @since 0.1
     */
    public static $graphql_enabled = false;


    /**
     * i18n translation domain
     *
     * @var string
     * @since 0.1
     */
    protected static $i18n_domain = 'theme-cpts';


    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     *
     * @var string
     * @since 1.3
     */
    protected static $i18n_base_lang = '';


    /**
     * Used to display male/female pronoun on concerned languages
     * Set true if should use female pronoun for this cpt
     *
     * @var bool
     * @since 1.0
     */
    public static $i18n_is_female = false;
}
```

<!-- tabs:end -->

You can override post type `args` or `labels` as you please inside their dedicated vars `$args_override/$labels_override`.  

> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_taxonomy/) or the [generate WP website](https://generatewp.com/taxonomy/) to have a listing of available arguments. 




## Initiate your Taxonomy 

ObjectPress manage the taxonomies initialisation out of the box for you. Just make sure to add your Taxonomy inside `taxonomies` conf key in `config/app.php` : 

```php
    /*
    |--------------------------------------------------------------------------
    | App taxonomies declaration
    |--------------------------------------------------------------------------
    |
    | Insert here you app/theme taxonomies
    | Format : 'taxonomy-identifier' => 'Path\To\Taxonomy\Class'
    |
    */
    'taxonomies' => [
        'example-taxonomy' => 'App\Taxonomies\ExampleTaxonomy',
    ],
```

> The taxonomiess are going to be initialized after custom post types and in the order they appears in the configuration array.

Alternatively, you can initiate your taxonomies manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on('init', function () {
    App\Taxonomies\ExampleTaxonomy::init();
});
```

## Helper methods

You can get some Taxonomy properties from it's class :

```php
use App\Taxonomies\ExampleTaxonomy;

ExampleTaxonomy::getDomain();      // => i18n translation domain
ExampleTaxonomy::getIdentifier();  // => WP Taxonomy identifier, eg: 'example-taxonomy'
```

## Single Term Taxonomy

Sometimes you may wish to allow only one term selection on a taxonomy. Thanks to WebDevStudios's [Taxonomy_Single_Term](https://github.com/WebDevStudios/Taxonomy_Single_Term/blob/master/README.md) class, we've integreated an easy way to force a single Term selection on your taxonomies, directly in your taxonomy definition class.

> Activate the single term mode and setup some optional params

```php
    /**
     * Activate 'single term' mode on this taxonomy 
     * 
     * @var bool
     * @since 1.3
     */
    public static $single_term = true;

    /**
     * 'single term' mode params (optional)
     * 
     * @var array
     * @since 1.3
     */
    public static $single_term_params = [
        'default_term' => 'my-default-value',
        'priority' => 'high',
    ];
```

##### Available params ($single_term_params)

| Key  | Type |  Description | Default |
|:---:|:---:|---|:---:|
| `default_term`  |  `string` or  `int` |  Default term to auto-select |  (none) |
| `priority`  | `string`  | Metabox priority (vertical placement). 'high', 'core', 'default' or 'low'   | `'low'`  |
| `context`  | `string`  | Metabox position (column placement). 'normal', 'advanced', or 'side'  | `'side'`  |
| `force_selection`  | `bool`  |  Set to true to hide "None" option & force a term selection |  `true` |
| `children_indented`  | `bool`  | Whether hierarchical taxonomy inputs should be indented to represent hierarchy  | `false`  |
| `allow_new_terms`  |  `bool` | Whether adding new terms via the metabox is permitted  |   `false` |