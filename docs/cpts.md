To create a custom post type, you first declare your custom post type properties in the `app/CustomPostTypes` folder, then initiate it thru the wordpress `function.php` file.  
 
## 1. Declare your CPT properties :

```php
<?php

namespace App\CustomPostTypes;

use OP\Framework\Boilerplates\CustomPostType;

class Example extends CustomPostType
{
    /**
     * i18n translation domain
     */
    public static $domain = '148-cpt';

    /**
     * Custom post type identifier
     */
    public static $cpt = 'example';


    /**
     * Singular and plural names of CPT
     */
    public static $singular = 'Example';
    public static $plural   = 'Examples';


    /**
     * Used to display 'un' or 'une'
     */
    public static $is_female = true;


    /**
     * Menu icon to display in back-office
     */
    public static $menu_icon = 'dashicons-admin-site-alt';


    /**
     * Enable graphql
     */
    public static $graphql_enabled = true;


    /**
     * CPT argument to overide over boilerplate
     */
    public static $args_override = [];


    /**
     * CPT labels to overide over boilerplate
     */
    public static $labels_override = [];
}

```

You can override post type `$args` or `$labels` as you please inside the `__construct()` method. Please refer to the [wordpress documentation](https://developer.wordpress.org/reference/functions/register_post_type/) to have the listing of available arguments. 


## 2. Initiate your CPT : 

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
    new App\CustomPostTypes\Example('148-cpts');
 
    // Register taxonomies
    ...
});
```


## 3. Create your model and move on !
> ℹ️ You can now create your Model to be able to fluently use your new CPT ! Consult the documentation.   

```php
<?php

use App\Models\Example;

$example = new Example();

$example->title = 'My new title';
$example->generatePermalink();       // https://yourwebsite.com/examples/my-new-title

$example->setThumbailFromUrl('https://example.com/my-awesome-image.jpg');

$example->setMeta('my-meta', 'Yes, it works !');

$example->save();
```