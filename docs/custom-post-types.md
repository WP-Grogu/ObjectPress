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
     * i18n string translation domain
     * 
     * @var string
     */
    public static $domain = 'theme-cpt';

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
     * i18n translation domain
     * 
     * @var string
     */
    public static $domain = 'theme-cpt';

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

    /**
     * Menu icon to display in back-office
     * 
     * @var string
     */
    public static $menu_icon = 'dashicons-admin-site-alt';

    /**
     * Enable graphql
     * 
     * @var bool
     */
    public static $graphql_enabled = true;

    /**
     * CPT argument to overide over boilerplate
     *
     * @var array
     */
    public static $args_override = [];

    /**
     * CPT labels to overide over boilerplate
     *
     * @var array
     */
    public static $labels_override = [];
}
```


<!-- tabs:end -->


You can override post type `args` or `labels` as you please inside their dedicated vars `$args_override/$labels_override`.

> Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_post_type/) or the [generate WP website](https://generatewp.com/post-type/) to have a listing of available arguments. 


## Initiate your custom post type

A custom post type should be initied during the `init` wordpress action.

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
    App\CustomPostTypes\Example::init();
 
    // Register taxonomies
    ...
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