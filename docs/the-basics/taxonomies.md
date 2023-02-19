# Taxonomies

Taxonomies in Wordpress are used to categorize posts.

With ObjectPress, you can optimise the custom taxonomy declaration, to gain time and easily enable/disable taxonomies in your theme.

The package also automatically generate a GraphQL schema for you if you're using the [WPGraphQL](https://www.wpgraphql.com/) plugin and enable it on the class.
 
## Defining properties

You'll most likely define your taxonomies inside the `app/Wordpress/Taxonomies/` directory.  

In the examples below, the class defined inside the `Minimal` tab is all you need to quickly initiate a taxonomy with ObjectPress. 
You can also adjust some more properties, displayed in the `Full` tab (shown values are ObjectPress defaults).



<!-- tabs:start -->

#### ** Minimal **

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class EventType extends Taxonomy
{
    /**
     * Taxonomy name
     *
     * @var string kebab-case
     */
    public static string $name = 'event-type';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public $singular = 'Event type';
    public $plural   = 'Event types';

    /**
     * On which post types this taxonomy will be registred
     *
     * @var array
     */
    protected $post_types = [
        'Event',
    ];
}
```


#### ** Full **

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class EventType extends Taxonomy
{
    /**
     * Taxonomy name
     *
     * @var string kebab-case
     */
    public static string $name = 'event-type';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     */
    public $singular = 'Event type';
    public $plural   = 'Event types';

    /**
     * On which post types this taxonomy will be registred
     *
     * @var array
     */
    protected $post_types = [
        'Event',
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

## Defining labels

The labels are automatically generated for you using the `singular` and `plural` properties, in your project language.

For some languages such as french, you'll need to tell ObjectPress to use female pronouns if needed (eg: "Ajouter un..." vs "Ajouter une...").

You can do that by using the `i18n_is_female` property :

```php
    /**
     * Used to display male/female pronoun on concerned languages
     * Set true if should use female pronoun for this cpt
     *
     * @var bool
     */
    public $i18n_is_female = false;
```

Of course, you can still override the labels manually, by using the `labels_override` property :

```php
    /**
     * CPT labels to overide over boilerplate
     *
     * @var array
     */
    public $labels_override = [
        'add_new_item' => 'Ajouter une nouvelle page',
    ];
```

## Initiate your Taxonomy 

ObjectPress manage the Taxonomy initialisation out of the box for you. You simply need to add your Taxonomy inside the `taxonomies` key in the `config/setup.php` configuration file : 

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
        App\Wordpress\Taxonomies\EventType::class,
    ],
```

> The taxonomies are initialized after post types and in the order they appears in the configuration array.

If you prefer, you can also initiate your custom post types manually :  

```php
<?php

use OP\Support\Facades\Theme;
use App\Wordpress\Taxonomies\EventType;

Theme::on('init', fn () => (new EventType)->boot()); # with Theme facade

add_action('init', fn () => (new EventType)->boot()); # with add_action
```

## Single Term Taxonomy

Sometimes you may wish to allow only one term selection on a taxonomy. Thanks to WebDevStudios's [Taxonomy_Single_Term](https://github.com/WebDevStudios/Taxonomy_Single_Term/blob/master/README.md) class, we've integreated an easy way to force a single Term selection on your taxonomies, directly in your taxonomy definition class.

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class EventType extends Taxonomy
{
    [...]

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
        'default_term'      => 'my-term-slug',  // Term name, slug or id
        'priority'          => 'default',       // 'high', 'core', 'default' or 'low'
        'context'           => 'side',          // 'normal', 'advanced', or 'side'
        'force_selection'   => true,            // Set to true to hide "None" option & force a term selection
        'children_indented' => false,
        'allow_new_terms'   => false,
    ];
}
```

## Manage your taxonomy with Models

You can now use the `Term` or `Taxonomy` Model to fluently use your new taxonomy.  

```php
Term::whereTaxonomy(EventType::$name)->orderByMeta('term_order')->get();  # Get all terms of the taxonomy
Taxonomy::name(EventType::$name)->slug('my-term-slug')->first();          # Get the taxonomy by slug
Taxonomy::name(EventType::$name)->where('parent', '0')->get();            # Get level 1 terms
```

Have a look at the [Models documentation](the-basics/models.md).  
