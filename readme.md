# Object Press
> *It's time to treat wordpress as an object*

## The concept


## Getting started

Thus this framework was built on top of [bedrock/sage](https://roots.io) stack, it is completely possible to use it out of the box. You'll simply need to setup composer with an autoload logic.

#### Installing via composer

To setup your app, you could use [composer](https://getcomposer.org).

// Settup composer.json

Your minimal theme's folder `composer.json` should looks like this :

```
{
    "name": "{your-author-name}/{your-package-name}",
    "description": "My awesome projectP",
    "type": "app",
    "authors": [
        {
            "name": "{author}",
            "email": "{mail}"
        }
    ],
     "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {
        "objectpress": 0.1
    }
}
```

And so your theme folder should have a `app` folder containing your CPTs, Taxonomies and Models.

### Minimal app folder structure

Your theme should include all your custom post types and models necessary for your app. You should at minimal have a `app/Interfaces/ICpts.php` file, containing an association of your postype-models :

```
<?php

namespace App\Interfaces;

interface ICpts
{
    const MODELS = [
        'page' => 'App\Models\Page',
        'post' => 'App\Models\Post',
        'example-cpt' => 'App\Models\ExampleCpt',
    ];
}
```

This is all you need to use the post model factory, working that way :

```
use OP\Framework\Models\Factory\PostModelFactory;


$post = PostModelFactory::model($post_id, 'example-cpt);
```

You could also create a new post this way :

```
use OP\Framework\Models\Factory\PostModelFactory;

$new_post = PostModelFactory::model(null, 'example-cpt);

// or

use App\Models\ExampleCpt;

$new_post = new ExampleCpt();
```

### The theme class

The theme class allows a fluent way of configuring your project settings.

```
$theme->addStyle('path/to/style.ccs')
	  ->addStyle('path/to/style2.ccs');
```



### Custom post types and taxonomies

#### Defining Custom post types (CPTs)

You can create a custom post type by creating a file inside your `app/CustomPostTypes` folder :

```
<?php

namespace App\CustomPostTypes;

use OP\Framework\Boilerplates\CustomPostType;

class Example extends CustomPostType
{
    protected static $domain;

    protected static $cpt = 'example';

    /**
     * Singular and plural names of CPT
     */
    public static $singular = 'Example';
    public static $plural   = 'Examples';
    
    /**
     * Enable graphql
     */
    public static $graphql_enabled = false;


    /**
     * Class constructor, register CTP to wordpress
     */
    public function __construct($domain)
    {
        static::$domain = $domain;

        $args_override = [
            'menu_icon' => 'dashicons-buddicons-activity',
            'rewrite'   => array('slug' => 'the-example')
        ];

        $labels_override = [];

        static::register($args_override, $labels_override);
    }
}

```

You can overide post type `$args`or `$labels` as you please inside the `__construct()` method.

> ❗️Don't forget to initate your CPTs in your function.php file

```
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
});
```

Working with taxonomies is similar, just create a file in `app/Taxonomies` folder :

```
<?php

namespace App\Taxonomies;

use OP\Framework\Boilerplates\Taxonomy;

class ExampleTaxonomy extends Taxonomy
{
    protected static $domain;

    protected static $taxonomy = 'example-taxonomy';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     */
    public static $singular = 'Example Taxonomy';
    public static $plural   = 'Example Taxonomies';


    /**
     * Enable graphql
     *
     * @var bool
     */
    public static $graphql_enabled = false;


    /**
     * Post types that will have the taxonomy
     *
     * @var array
     */
    protected static $post_types = [
        'post',
        'example',
    ];

    /**
     * Class constructor, register CTP to wordpress
     */
    public function __construct($domain)
    {
        static::$domain = $domain;

        $args_override   = [];
        $labels_override = [];

        static::register($args_override, $labels_override);
    }
}

```

> ❗️Don't forget to initate your Taxonomies in your function.php file

```
<?php

/**
 * Theme configuration class
 */
$theme = OP\Framework\Theme::getInstance();


/**
 * Post types & taxonomies initialisation
 */
$theme->on('init', function () {
    // Register Taxonomies
    new App\Taxonomies\ExampleTaxonomy('148-cpts');
});
```

### Models

#### Defining models

You can define your models in the `App\Models` namespace (`app/Models` folder) :

```
<?php

namespace App\Models;

use OP\Framework\Models\PostModel;

class Example extends PostModel
{
    /**
     * Wordpress post_type associated to the current model
     */
    public static $post_type = 'example';
}

```



#### Using models

Models are a way to treat your custom post types, including `post` and `page` post type.

`PostModel` contains various methods allowing you to treat your posts :

```
use App\Models\Example;

$example = new Example($example_id);

$permaink   = $example->permalink();
$metas      = $example->metas();
$taxonomies = $example->getTaxonomies();

$post_date = $example->postDate('d-m-Y');

$example->setTaxonomyTerms('taxonomy-name', [
	100,
	101,
]);

$example->setThumbailFromUrl("https://images.com/my-post-image.png");

$example->setMeta('meta_key', $meta_value);

$example->publish();
$example->trash();
```

You can also mass query your models :

```
Example::all();
```


You can easily change post properties :

```
use App\Models\Example;

$post = Example::current();
`
$post->title = "It's done, and can't be unmade."; // change the post_title, doesn't affect database
$post->save(); // save your changes in database

```

> Changing post properties needs a `save()` in order to affect database.
