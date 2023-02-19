# Quick start

## Installation

The recommanded way is to use [composer](https://getcomposer.org) to manage your PHP project dependancies, including ObjectPress.  

> If you're using the [roots.io](https://roots.io) stack, you may chose to include ObjectPress directly in the bedrock vendors so you may use ObjectPress in mu-plugin directories as well.

```sh
# Get latest stable version
composer require tgeorgel/objectpress

# Get a specific version
composer require "tgeorgel/objectpress:^2.1.0"
```

> You can check out available versions on [Packagist](https://packagist.org/packages/tgeorgel/objectpress), or directly on the [Github repository](https://github.com/WP-Grogu/ObjectPress/tags).

!> If you don't already have a composer.json file in your theme, first init composer using `composer init` command.


## The app directory

You can now create an `app` folder in your theme directory. This is the place where belongs all your classes, you'll setup from here your Post types, your Taxonomies, your Models, and so on.

### Autoloading

If you're not using one yet, you can use composer to fastly setup a psr-4 autoload. Add the following configuration to your `composer.json` file at root of your theme dir :

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "require": {...}
}
```

!> ObjectPress assume your theme is using the `App` namespace. However, you are free to change this inside the `config/object-press.php` [configuration file](getting-started/configuration.md). 

### App folder structure

From now, you should have **ObjectPress**, an `app/` folder, and an autoload logic. What does reside in this `app/` directory ?

Well, whatever you want. A typical project would include a similar structure :  

```json
app/
   Wordpress/
      PostTypes/
      Taxonomies/
      Hooks/
      Commands/
      Roles/
   Models/
      User.php                // Your user model
      Post.php                // Your post model (any post_type)
      Page.php                // Your `page` post_type model
      Event.php               // Your `event` post_type model
      Term.php                // Your term model (any taxonomy)
   Helpers/
   Api/
   Providers/
   ...
```

You can find a starter theme folder using this `app/` folder structure [here](https://gitlab.com/tgeorgel/object-press-base-theme-directory).  


## Initiate ObjectPress

To initiate ObjectPress, you need to call the `boot()` method, which accept a path to your config directory.

Drop this inside your `function.php` file : 

```php
/*
|--------------------------------------------------------------------------
| Start writing models
|--------------------------------------------------------------------------
|
| We now boot ObjectPress package in order to use Models, PostType and more.
| Read online documentation at https://object-press.wp-grogu.dev/#/README
|
*/

OP\Support\Facades\ObjectPress::boot(__DIR__ . '/config');
```

> On Roots.io stack, you can add this code after Acorn's Bootloader.

!> Do not call this method inside a Wordpress hook, ObjectPress will automatically boot stuff in the corresponding Wordpress lifeycle hook for you. 


## Not using composer ?

If you don't want to use composer, you can instead download / clone the repository, and then move the ObjectPress directory wherever you wish, in your Wordpress theme folder or as a mu-plugin.
Don't forget to run the `composer install` command inside ObjectPress folder, otherwise it won't load the required dependancies. You can then remove the `.gitignore` file and the `.git` folder so your dependancie gets deployed with your code in production.

You will then need to include ObjectPress's `index.php` file within your app, typically on top of your theme `functions.php` file.

You will need to setup an autoload logic in order to make ObjectPress work properly, so the `app/` directory is autoloaded.