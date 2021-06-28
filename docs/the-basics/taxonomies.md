# Taxonomies

Taxonomies are Wordpesss posts categories.
With ObjectPress, you want to optimise this taxonomy creation, to gain time and easily enable/disable taxonomies in your theme.
 
## Defining your taxonomy properties

Define your taxonomies inside the `app/Wordpress/Taxonomies/` folder. 
The class defined inside the `Minimal` tab is all you need to quickly initiate a Taxonomy with ObjectPress. 
You can adjust some properties properties, showed in the `Full` tab (shown values are ObjectPress defaults).



<!-- tabs:start -->

#### ** Minimal **

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class StoreType extends Taxonomy
{
    /**
     * Taxonomy identifier
     *
     * @var string
     */
    protected $name = 'store-type';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public $singular = 'Store type';
    public $plural   = 'Store types';

    /**
     * On which post types this taxonomy will be registred
     *
     * @var array
     */
    protected $post_types = [
        'Store',
    ];
}
```


#### ** Full **

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class StoreType extends Taxonomy
{
    /**
     * Taxonomy identifier
     *
     * @var string
     */
    protected $name = 'store-type';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public $singular = 'Store type';
    public $plural   = 'Store types';

    /**
     * On which post types this taxonomy will be registred
     *
     * @var array
     */
    protected $post_types = [
        'Store',
    ];


    /**
     * Activate 'single term' mode on this taxonomy
     *
     * @var bool
     */
    public $single_term = false;


    /**
     * 'single term' mode params 
     * 
     * @var array
     */
    public $single_term_params = [
        'default_term' => 'my-category',
    ];

    /**
     * CPT/Taxonomy argument to overide over boilerplate
     *
     * @var array
     */
    public $args_override = [];


    /**
     * CPT/Taxonomy labels to overide over boilerplate
     *
     * @var array
     */
    public $labels_override = [];


    /**
     * Enable graphql on this CPT/Taxonomy
     *
     * @var bool
     */
    public $graphql_enabled = false;


    /**
     * i18n translation domain
     *
     * @var string
     */
    protected $i18n_domain = 'theme-cpts';


    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     *
     * @var string
     */
    protected $i18n_base_lang = '';


    /**
     * Used to display male/female pronoun on concerned languages
     * Set true if should use female pronoun for this cpt
     *
     * @var bool
     */
    public $i18n_is_female = false;
}
```

<!-- tabs:end -->

> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_taxonomy/) or the [generate WP website](https://generatewp.com/taxonomy/) to have a listing of available arguments and labels, overritable by using their dedicated variables `$args_override` and `$labels_override`. 



## Initiate your Taxonomy 


ObjectPress manage the custom post types initialisation out of the box for you. You simply need to add your Taxonomy inside the `taxonomies` key, inside the `config/app.php` config file :

> The taxonomies are going to be initialized after custom post types and in the order they appears in the configuration array.

```php
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
        App\Wordpress\Taxonomies\StoreType::class,
    ],
```

Alternatively, you can initiate your taxonomies manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on('init', function () {
    (new App\Wordpress\Taxonomies\StoreType)->boot();
});
```

## Single Term Taxonomy

Sometimes you may wish to allow only one term selection on a taxonomy. Thanks to WebDevStudios's [Taxonomy_Single_Term](https://github.com/WebDevStudios/Taxonomy_Single_Term/blob/master/README.md) class, we've integreated an easy way to force a single Term selection on your taxonomies, directly in your taxonomy definition class.

> Activate the single term mode and setup some optional params

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class StoreType extends Taxonomy
{
    /** Previous settings.. **/


    /**
     * Activate 'single term' mode on this taxonomy 
     * 
     * @var bool
     */
    public $single_term = true;


    /**
     * Single term box type ('select' or 'radio', default to radio)
     *
     * @var string
     */
    public $single_term_type = 'radio';


    /**
     * 'single term' mode params (optional)
     * 
     * @var array
     */
    public $single_term_params = [
        'default_term' => 'my-default-value',
        'priority'     => 'high',
    ];
}
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