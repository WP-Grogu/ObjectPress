# Quick start

## Introduction

Thus this framework was built on top of [bedrock/sage](https://roots.io) stack, it is completely possible to use it out of the box. You'll simply need to setup a `composer.json` file with an autoload logic.

## Installing
### Installing via composer

To setup your app, you should use [composer](https://getcomposer.org).

```
composer require tgeorgel/objectpress
```

You could specify a version tag in your composer file to avoid any breaking changes. To install a specific version :  

```
composer require "tgeorgel/objectpress ~1.0.1"
```

### Installing manually

Download or clone the repository, and put the folder wherever you wish, in your wordpress theme folder.
Don't forget to run a `composer install` inside the ObjectPress folder, otherwise it won't find dependant classes.

> If you're using bedrock/sage stack, you could also decide to put ObjectPress as a mu-plugin, althought you would loose the magic of composer :)



## Setup your app folder

### Setup autoload

You can use composer to setup a psr-4 autoloading logic. Create a `composer.json` file in your theme directory, using  the command

```
composer init
```

Then add the autoload config :  

```json
{
    ...
     "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {
        "tgeorgel/objectpress": "~1.0.1"
    }
    ...
}
```

> ⚠ ObjectPress assume your theme is using the `App` namespace, if it's not the case you should manually override the `OP\Framework\Models\Factory\PostModelFactory` class by extending it. 

Alternatively, you can use a custom autoload logic.   

You can now put your app files inside the `app` folder in the theme directory. This is the place where you put your Custom post types, Taxonomies, Models and so on.  

### Minimal app folder structure

Your theme should include all your custom post types and models necessary for your app. A typical app folder has the following structure :  

```json
app/
   CustomPostTypes/
      Example.php             // Define the `example` wordpress custom post type
   Taxonomies/
      ExampleTaxonomy.php     // Define the `example_taxonomy` wordpress taxonomy
   Models/
      Post.php                // Your post model
      Page.php                // Your page model
      User.php                // Your user model
      Example.php             // Your example model
   Interfaces/
      ICpts.php               // Your custom post type interface, binding wp cpt to your models
   Helpers/
   Controllers/
   Utils/
   ...
```

You can find an example theme folder structure [here](https://gitlab.com/tgeorgel/object-press-base-theme-directory).  

Please read the dedicated pages for [Custom Post Types](Custom-Post-Types), [Taxonomies](Taxonomies), [Models](Models/Introduction) and so on.  

ℹ️ The `app/Interfaces/ICpts.php` file contains an association of your postype-models :

```php
<?php

namespace App\Interfaces;

interface ICpts
{
    const MODELS = [
        'page' => 'App\Models\Page',
        'post' => 'App\Models\Post',
        'example-cpt' => 'App\Models\Example',
    ];
}
```

This is all you need to use the post model factory, working that way :

```php
use OP\Framework\Models\Factory\PostModelFactory;

$post = PostModelFactory::model($post_id, 'example-cpt');
```

To learn more about the powerfull use of models, consult the [wiki page](Models).


## The theme class

The theme class allows a fluent way of configuring your project settings.

```php
/// ** function.php ** ///


/**
 * Theme configuration class
 */
$theme = OP\Framework\Theme::getInstance();


/**
 * Post types & taxonomies initialisation
 */
$theme->on('init', function () {
    // Register CPTs
    new App\CustomPostTypes\Example('theme-cpts');
    new App\CustomPostTypes\City('theme-cpts');
    
    // Register Taxonomies
    new App\Taxonomies\Department('theme-cpts');
    new App\Taxonomies\PriceRange('theme-cpts');
});


/**
 * Add my theme styles to WP styles queue
 */
$theme->addStyle('path/to/style.ccs')
	  ->addStyle('path/to/style2.ccs');
```


Read more about the Theme class on the dedicated [wiki page](Theme-class).  