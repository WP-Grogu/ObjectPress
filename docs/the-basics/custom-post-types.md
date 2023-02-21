# Post types

Custom post types in Wordpress are used to groups some type of posts together.  

With ObjectPress, you can optimise the custom post type declaration, to gain time and easily enable/disable custom post types in your theme.

The package automatically generate labels and defaut arguments for you, so you can focus on the essentials.

The package also automatically generate a GraphQL schema for you if you're using the [WPGraphQL](https://www.wpgraphql.com/) plugin and enable it on the class.
 
## Defining properties

You'll most likely define your post types inside the `app/Wordpress/PostTypes/` directory.  

In the examples below, the class defined inside the `Minimal` tab is all you need to quickly initiate a post type with ObjectPress. 
You can also adjust some more properties, displayed in the `Full` tab (shown values are ObjectPress defaults).

<!-- tabs:start -->

#### ** Minimal **

```php
<?php

namespace App\Wordpress\PostTypes;

use OP\Framework\Wordpress\PostType;

class Event extends PostType
{
    /**
     * Custom post type name
     *
     * @var string kebab-case
     */
    public static string $name = 'event';

    /**
     * Singular and plural names of CPT
     * 
     * @var string
     */
    public $singular = 'Event';
    public $plural   = 'Events';

    /**
     * Menu icon to display in back-office (dash-icon)
     *
     * @var string
     * @since 1.0.3
     */
    public $menu_icon = 'dashicons-store';
}
```

#### ** Full **

```php
<?php

namespace App\Wordpress\PostTypes;

use OP\Framework\Wordpress\PostType;

class Event extends PostType
{
    /**
     * Custom post type name
     *
     * @var string kebab-case
     */
    public static string $name = 'event';

    /**
     * Singular and plural names of CPT
     *
     * @var string
     */
    public $singular = 'Event';
    public $plural   = 'Events';

    /**
     * Menu icon to display in back-office (dash-icon)
     *
     * @var string
     */
    public $menu_icon = 'dashicons-store';

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

    /**
     * Enable graphql on this CPT
     *
     * @var bool
     */
    public $graphql_enabled = false;

    /**
     * CPT argument to overide over boilerplate
     *
     * @var array
     */
    public $args_override = [];

    /**
     * CPT labels to overide over boilerplate
     *
     * @var array
     */
    public $labels_override = [];
}
```


<!-- tabs:end -->


> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_post_type/) or the [generate WP website](https://generatewp.com/post-type/) to have a listing of available arguments and labels, overritable by using their dedicated variables `$args_override` and `$labels_override`. 

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


## Initiate your custom post type

ObjectPress manage the Post types initialisation out of the box for you. You simply need to add your Custom post type inside the `cpts` key in the `config/setup.php` configuration file : 

```php
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
        App\Wordpress\PostTypes\Event::class,
    ],
```

> The custom post types are initialized before taxonomies and in the order they appears in the configuration array.


If you prefer, you can also initiate your custom post types manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on(
    'init', 
    fn () => (new App\Wordpress\PostTypes\Event)->boot()
);
```


## Bind a model to your post type

You can now create your Model to fluently use your new post type.  

```php
use App\Wordpress\PostTypes\Event;

$event = Event::create([
    'title' => 'Meeting with the team',
    'slug'  => 'meeting-with-the-team',
]);

$event->slug  = 'my-new-slug';
$event->save();

$event->setMeta('my-meta', 'Yes, this works.');

Event::slug('my-new-slug')->first(); // returns the event.
```

Have a look at the [Models documentation](the-basics/models.md).  
