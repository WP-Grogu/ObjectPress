# Post types

Custom post types are the way wordpress manage particular type of data.
With ObjectPress, you want to optimise this custom post type creation, to gain time and easily enable/disable custom post types in your theme.
 
## Defining custom post types properties

Define your custom post types inside the `app/Wordpress/PostTypes/` folder. 
The class defined inside the `Minimal` tab is all you need to quickly initiate a Custom post type with ObjectPress. 
You can adjust some properties properties, showed in the `Full` tab (shown values are ObjectPress defaults).

<!-- tabs:start -->

#### ** Minimal **

```php
<?php

namespace App\Wordpress\PostTypes;

use OP\Framework\Wordpress\PostType;

class Store extends PostType
{
    /**
     * Custom post type identifier
     * 
     * @var string
     */
    protected $name = 'store';

    /**
     * Singular and plural names of CPT
     * 
     * @var string
     */
    public $singular = 'Store';
    public $plural   = 'Stores';

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

class Store extends PostType
{
    /**
     * Custom post type name/key
     * @var string
     */
    protected $name = 'store';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     */
    public $singular = 'Store';
    public $plural   = 'Stores';


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


## Initiate your custom post type

ObjectPress manage the custom post types initialisation out of the box for you. You simply need to add your Custom post type inside the `cpts` key, inside the `config/app.php` config file : 

> The custom post types are going to be initialized before taxonomies, and in the order they appears in the configuration array.

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
        App\Wordpress\PostTypes\Store::class,
    ],
```

Alternatively, you can initiate your custom post types manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on('init', function () {
    (new App\Wordpress\PostTypes\Store)->boot();
});
```


## Bind a model to your custom post type

You can now create your Model to be able to fluently use your new CPT, the OOP way !  
Please read the [Models documentation](the-basics/models.md)  

```php
use App\Wordpress\PostTypes\Store;

$store = Store::create();

$store->title = 'My first store !';
$store->slug  = 'my-first-store';
$store->save();

$store->setMeta('my-meta', 'Yes, it works !');
```