# Quick start

## Installation
#### via composer

The best practice to manage your dependancy is to use [composer](https://getcomposer.org).  

!> If you don't already have a composer.json file in your theme, first init composer using `composer init` command.

To install ObjectPress, add it to your composer dependancies : 

```sh
# Get latest stable version
composer require tgeorgel/objectpress

# Get a specific version
composer require "tgeorgel/objectpress ~v1.0.4"
```

> You can check available versions on [Packagist](https://packagist.org/packages/tgeorgel/objectpress), or directly on the [Gitlab repository](https://gitlab.com/tgeorgel/object-press/-/tags).

#### manually

If you don't want to use composer, you can instead download or clone the repository, and put the ObjectPress folder wherever you wish, in your wordpress theme folder.
Don't forget to run a `composer install` inside the ObjectPress folder, otherwise it won't have the required dependancies.

You should then include ObjectPress's `index.php` file within your app, at top of your `functions.php` file for example.

!> You'll need at [autoload](quickstart.md?id=setup-autoload) logic in order to ObjectPress to work properly

> If you're using bedrock/sage stack, you could also decide to put ObjectPress as a mu-plugin, althrought you would loose the magic of composer :)



## The app directory

### Setup autoload

You can use composer to setup a psr-4 autoloading logic. Add the following configuration to your `composer.json` file :


```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {
        "tgeorgel/objectpress": "^1.0"
        ... whatever dependancies
    }
}
```

!> ObjectPress assume your theme is using the `App` namespace, and will look into this namespace. 

Alternatively, you can use a custom autoload logic.   

You can now create an `app` folder in your theme directory. This is the place where belongs all your classes, you'll setup from here your Custom post types, your taxonomies, your models, and so on.

### Minimal app folder structure

From now, you should have **ObjectPress**, an `app/` folder, and an autoload logic. But what should you have inside the `app/` directory ?

A typical app folder has the following structure :  

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

   // Some more folders you should expect in the `app/` directory
   Helpers/
   Controllers/
   Utils/
   Api/
   GraphQL/
   ...
```

You can find a starter theme folder using this `app/` folder structure [here](https://gitlab.com/tgeorgel/object-press-base-theme-directory).  


## Initiate ObjectPress

In your `function.php`, you need to initiate the frameworks, and so define necessary constants, load config files, initiate Custom post types, taxonomies, API routes and so on.

```php
<?php

use OP\Support\ObjectPress;

ObjectPress::init();
```