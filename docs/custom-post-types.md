# Custom post types

Custom post types are the way wordpress manage particular type of data.
With ObjectPress, you want to optimise this custom post type creation, to gain time and easily enable/disable custom post types in your theme.

This class is inspired by [generate wordpress](https://generatewp.com/post-type/) style, you can checkup `labels` and `args` overwritable on their website. 
 
## Defining custom post types properties

Define your custom post types inside the `app/CustomPostTypes` folder. You can start with the `Minimal` template, and add needed properties displayed in the `Full` tab (displayed values are defaults).

<!-- tabs:start -->

#### ** Minimal **

```php
<?php

namespace App\CustomPostTypes;

use OP\Framework\Boilerplates\CustomPostType;

class Example extends CustomPostType
{
    /**
     * Custom post type identifier
     * 
     * @var string
     */
    public static $cpt = 'example';

    /**
     * Singular and plural names of CPT
     * 
     * @var string
     */
    public static $singular = 'Example';
    public static $plural   = 'Examples';
}
```

#### ** Full **

```php
<?php

namespace App\CustomPostTypes;

use OP\Framework\Boilerplates\CustomPostType;

class Example extends CustomPostType
{
    /**
     * Custom post type name/key
     * @var string
     * @since 0.1
     */
    protected static $cpt = 'example';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     * @since 0.1
     */
    public static $singular = 'Example';
    public static $plural   = 'Examples';


    /**
     * Menu icon to display in back-office (dash-icon)
     *
     * @var string
     * @since 1.3
     */
    public static $menu_icon = 'dashicons-book';


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


    /**
     * Enable graphql on this CPT
     *
     * @var bool
     * @since 0.1
     */
    public static $graphql_enabled = false;


    /**
     * CPT argument to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $args_override = [];


    /**
     * CPT labels to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $labels_override = [];
}
```


<!-- tabs:end -->


You can override post type `args` or `labels` as you please inside their dedicated vars `$args_override/$labels_override`.

> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_post_type/) or the [generate WP website](https://generatewp.com/post-type/) to have a listing of available arguments. 


## Initiate your custom post type

ObjectPress manage the custom post types initialisation out of the box for you. Just make sure to add your Custom post type inside `cpts` conf key in `config/app.php` : 

```php
    /*
    |--------------------------------------------------------------------------
    | App custom post types declaration
    |--------------------------------------------------------------------------
    |
    | Insert here your app/theme custom post types
    | Format : 'cpt-identifier' => 'Path\To\CustomPostType\Class'
    |
    */
    'cpts' => [
        'example' => 'App\CustomPostTypes\Example',
    ],
```

> The custom post types are going to be initialized before taxonomies and in the order they appears in the configuration array.

Alternatively, you can initiate your custom post types manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on('init', function () {
    App\CustomPostTypes\Example::init();
});
```


## Bind a model to you custom post type

You can now create your Model to be able to fluently use your new CPT, the OOP way !
Consult the [Models documentation](models.md)  

```php
$example = new App\Models\Example();

$example->title = 'My first example';
$example->setMeta('my-meta', 'Yes, it works !');
$example->save();
```


## Helper methods

You can get some Custom post type properties from it's class :

```php
use App\CustomPostTypes\Example;

Example::getDomain();      // => i18n translation domain
Example::getIdentifier();  // => WP CPT identifier, eg: 'example'
```