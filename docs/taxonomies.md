# Custom post types

Taxonomies are Wordpesss posts categories.
With ObjectPress, you want to optimise this taxonomy creation, to gain time and easily enable/disable taxonomies in your theme.

This class is inspired by [generate wordpress](https://generatewp.com/taxonomy/) style, you can checkup `labels` and `args` overwritable on their website. 
 
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
    protected static $taxonomy = 'custom-taxonomy';


    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     * @since 0.1
     */
    public static $singular = 'Custom Taxonomy';
    public static $plural   = 'Custom Taxonomies';


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
     * i18n cpt default lang (format: 'en', 'fr'..)
     * Leave empty string to use the app default lang
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


## Single Term Taxonomy

Sometimes you may wish to allow only one term selection on a taxonomy. Thanks to WebDevStudios's [Taxonomy_Single_Term](https://github.com/WebDevStudios/Taxonomy_Single_Term/blob/master/README.md) class, we've integreated an easy way to force a single Term selection on your taxonomies.

> Activate the single term mode and setup some optionnal params

```php
    /**
     * Activate 'single term' mode on this taxonomy 
     * 
     * @var bool
     * @since 1.3
     */
    public static $single_term = true;

    /**
     * 'single term' mode params (optionnal)
     * 
     * @var array
     * @since 1.3
     */
    public static $single_term_params = [
        'default_term' => 'my-default-value',
        'priority' => 'high',
    ];
```
##### Available params

| Key  | Type |  Description | Default |
|---|---|---|---|
| `default_term`  |  `string`, `int` |  Default term to auto-select |  (none) |
| `priority`  | `string`  | Metabox priority. (vertical placement). 'high', 'core', 'default' or 'low'   | `'low'`  |
| `context`  | `string`  | Metabox position. (column placement). 'normal', 'advanced', or 'side'  | `'side'`  |
| `force_selection`  | `bool`  |  Set to true to hide "None" option & force a term selection |  `true` |
| `children_indented`  | `bool`  | Whether hierarchical taxonomy inputs should be indented to represent hierarchy  | `false`  |
| `allow_new_terms`  |  `bool` | Whether adding new terms via the metabox is permitted  |   `false` |