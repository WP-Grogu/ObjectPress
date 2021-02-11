# The Theme class

The theme class allows a fluent way of configuring your theme settings. It globally calls wordpress functions, statically and properly. This class also contains helper functions.  

Most methods are chainable. 


## Helper Methods

You can analyze the [Theme class](https://gitlab.com/tgeorgel/object-press/-/blob/master/src/Core/Theme.php) to see which function are made available to you.

```php
// function.php

use OP\Support\Facades\Theme;


/**
 * Add my theme styles to WP styles queue
 */
Theme::addStyle('style1', '/path/to/style.ccs')
     ->addStyle('style1', '/path/to/style2.ccs')
     ->addScript('theme-scripts', '/path/to/scripts.js');


/**
 * Register nav menus
 */
Theme::addNavMenus([
    'main'   => 'Main Menu',
    'footer' => 'Footer Menu',
]);
```

## Hooking

Following the wordpress method name, you can use `Theme::addAction()` or `Theme::addFilter()` methods to register hooks.  
However, you can make use of the `on()` method, which supports an array of actions to register the function to.   
All hooks functions are chainable.  

```php
/**
 * Force e-mails content type to HTML
 */
Theme::on('wp_mail_content_type', function () {
    return 'text/html';
});


/**
 * Replace front urls origin for an headless theme (using custom function `replaceFrontOrigin`)
 */
Theme::on(['post_link', 'page_link', 'post_type_link', 'term_link'], function ($post_link) {
    return replaceFrontOrigin($post_link);
}, 101);
```