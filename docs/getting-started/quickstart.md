# Quick start

## Installation
#### using composer

The best practice to manage your dependancy is to use [composer](https://getcomposer.org).  

!> If you don't already have a composer.json file in your theme, first init composer using `composer init` command.

To install ObjectPress, add it to your composer dependancies : 

```sh
# Get latest stable version
composer require tgeorgel/objectpress

# Get a specific version
composer require "tgeorgel/objectpress ~v2.0"
```

> You can check out available versions on [Packagist](https://packagist.org/packages/tgeorgel/objectpress), or directly on the [Gitlab repository](https://gitlab.com/tgeorgel/object-press/-/tags).

#### manually

If you don't want to use composer, you can instead download or clone the repository, and put the ObjectPress folder wherever you wish, in your wordpress theme folder or as a mu-plugin.
Don't forget to run the `composer install` command inside ObjectPress folder, otherwise it won't load the required dependancies.

You should then include ObjectPress's `index.php` file within your app, at top of your `functions.php` file for example.

!> You will need to setup an [autoload](quickstart.md?id=setup-autoload) logic in order to make ObjectPress work properly

> If you're using bedrock/sage stack, you could also decide to put ObjectPress as a mu-plugin, althrought you would loose the magic of composer :)



## The app directory

### Setup autoload

If you're not using one yet, you can use composer to fastly setup a psr-4 autoload. Add the following configuration to your `composer.json` file at root of your theme dir :


```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {
        "tgeorgel/objectpress": "^2.0"
        ... whatever dependancies
    }
}
```

Alternatively, you can use a custom autoload logic.   

You can now create an `app` folder in your theme directory. This is the place where belongs all your classes, you'll setup from here your Post types, your Taxonomies, your Models, and so on.

!> ObjectPress assume your theme is using the `App` namespace, and will look into this namespace. However, you can change this thru `config/object-press.php` conf file. 

### Minimal app folder structure

From now, you should have **ObjectPress**, an `app/` folder, and an autoload logic. But what should you have inside the `app/` directory ?

A typical app folder has the following structure :  

```json
app/
   Wordpress/
      PostTypes/
        Service.php           // Define the `service` wordpress custom post type
      Taxonomies/
        ServiceType.php       // Define the `service_type` wordpress taxonomy
      Hooks/
        ThemeSetup.php        // Setup the theme, hooking on a wordpress action
   Models/
      User.php                // Your user model
      Post.php                // Your post model (manages all post types)
      Page.php                // Your `page` post type model
      Service.php             // Your `service` post type model

   // Some more folders you should expect in the `app/` directory
   Helpers/
   Api/
   Controllers/
   ...
```

You can find a starter theme folder using this `app/` folder structure [here](https://gitlab.com/tgeorgel/object-press-base-theme-directory).  


## Initiate ObjectPress

In your `function.php`, you need to initiate the frameworks, and so define necessary constants, load config files, initiate Custom post types, taxonomies, API routes and so on. 

!> You must not call the ObjectPress inizialisation inside a hook, as ObjectPress does this in the background. 

```php
<?php

use OP\Support\Facades\ObjectPress;

ObjectPress::init('/optionnal/path/to/config/folder');
```