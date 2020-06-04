# Quick start

## Introduction

Thus this framework was built on top of [bedrock/sage](https://roots.io) stack, it is completely possible to use it out of the box. You'll simply need to setup a `composer.json` file with an autoload logic.

## Installing
### Installing via composer

To setup your app, you should use [composer](https://getcomposer.org).

```sh
composer init                           # If you don't already have a composer.json file in you theme folder 
composer require tgeorgel/objectpress
```

You could specify a version tag in your composer file to avoid any breaking changes. To install a specific version :  

```
composer require "tgeorgel/objectpress ~v1.0.3"
```

### Installing manually

Download or clone the repository, and put the folder wherever you wish, in your wordpress theme folder.
Don't forget to run a `composer install` inside the ObjectPress folder, otherwise it won't find dependant classes.

You should then include ObjectPress's `index.php` file within your app, inside you `functions.php` file for example.

> If you're using bedrock/sage stack, you could also decide to put ObjectPress as a mu-plugin, althrought you would loose the magic of composer :)



## Setup your app folder

### Setup autoload

You can use composer to setup a psr-4 autoloading logic. Create a `composer.json` file in your theme directory, using  the command


Then add the autoload config :  

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {
        "tgeorgel/objectpress": "~1.0.1"
    }
}
```

!> ObjectPress assume your theme is using the `App` namespace. 
> Alternatively, you can use a custom autoload logic.   

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


## Function.php

In your `function.php`, you should have at least a call to `OP\Core\Container::getInstance();`, it will define necessary constants.

```php
<?php

$theme = OP\Framework\Theme::getInstance();


/**
 * Init OP and theme CPTs & Taxonomies
 */
$theme->on('init', function () {
    // Init ObjectPress
    OP\Core\Container::getInstance();

    // Init CPTs
    // App\CustomPostTypes\Example::init();

    // Init Taxonomies
    // App\Taxonomies\ExampleTaxonomy::init();
});

```

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


Read more about the Theme class on the dedicated [wiki page](theme-class.md).  