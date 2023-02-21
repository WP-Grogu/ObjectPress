# APIs


## Introduction

API routes in ObjectPress are represented by classes. You **must** set one route per class.  
ObjectPress automatically manage validation and request setup for you.

## Define an API route


Define your API routes inside the `app/Api/` folder. 

<!-- tabs:start -->

#### ** Minimal, without parameters **

```php
<?php

namespace App\Api;

use OP\Framework\Api\ApiRoute;

/**
 * API Route : GET /wp-json/articles/v1/fetch
 */
class FetchArticles extends ApiRoute
{
    /**
     * Api route namespace
     *
     * @var string
     */
    public static $namespace = 'articles';

    /**
     * Api route
     *
     * @var string
     */
    public static $route = '/fetch';

    /**
     * HTTP method
     *
     * @var string|array (GET|POST|PUT|DELETE|UPDATE)
     */
    public static $methods = 'GET';

    /**
     * Resolve the API route.
     * 
     * @return mixed
     */
    public static function resolve(object $args, object $body_args) 
    {
        return [
            //
        ];
    }
}
```


#### ** Full, with parameters **

```php
<?php

namespace App\Api;

use OP\Framework\Api\ApiRoute;

/**
 * API Route : GET /wp-json/contact/v1/form
 */
class ContactForm extends ApiRoute
{
    /**
     * Api version
     *
     * @var string
     */
    public static $version = 'v1';

    /**
     * Api route namespace
     *
     * @var string
     */
    public static $namespace = 'contact';

    /**
     * Api route
     *
     * @var string
     */
    public static $route = '/form';

    /**
     * HTTP method
     *
     * @var string|array (GET|POST|PUT|DELETE|UPDATE)
     */
    public static $methods = 'GET';

    /**
     * Route parameters / variables
     *
     * @var array
     */
    public static $args = [
        'id' => [
            'rules' => ['required', 'numeric'],
        ],
    ];

    /**
     * Route Body parameters / variables
     *
     * @var array
     */
    public static $body_args = [
        'name' => [
            'rules' => 'required|alpha_numeric',
        ],
        'email' => [
            'rules' => ['required', 'email'],
        ],
        'phone' => [
            'validate_callback' => [static::class, 'validatePhone'],
        ],
    ];

    /**
     * Resolve the API route.
     * 
     * @return mixed
     */
    public static function resolve(object $args, object $body_args) 
    {
        return [
            'id'    => $args->id;
            'name'  => $body_args->name;
            'email' => $body_args->email;
            'phone' => $body_args->phone;
        ];
    }
}
```

<!-- tabs:end -->

## Initiate an API Route

ObjectPress manage the API routes initialisation out of the box for you. You simply need to add your API class inside the `apis` key, inside the `config/app.php` config file : 

```php
    /*
    |--------------------------------------------------------------------------
    | App APIs declaration
    |--------------------------------------------------------------------------
    |
    | Insert here you app/theme api routes
    | Format : 'namespace/route' => 'Path\To\Api\Class'
    |
    */
    'apis' => [
        App\Api\FetchArticles::class,
    ],
```


## Validation

ObjectPress offers the possibility to automatically apply validation over your endpoint arguments.
- The `$args` property define the expected URL parameters  
- The `$body_args` property define the expected JSON body parameters  

ObjectPress relies on the [Laravel validation](https://laravel.com/docs/8.x/validation) package, and therefore shares the exact same functionalities. Please have a look at the Laravel documentation to get a listing of available [validation rules](https://laravel.com/docs/8.x/validation#available-validation-rules).

> The `rules` key can be an array of rules, or a string containing all rules separated by `|`, exactly as in Laravel.

If you wish to use a custom validation logic, you can specify your validation method (as a `callable` type) using the `validate_callback` parameter.

```php
public static $body_args = [
    'generic_arg' => [
        'validate_callback' => 'method',   # Define a custom validation callback mathed.
    ],
    'custom' => [
        'validate_callback' => [           # Define a custom validation callback method, inside a class.
            static::class, 
            'class_method'
        ],
    ],
    'email' => [
        'rules' => ['required', 'email']   # Use Illuminate Validator rules.
    ],
    'not_validated' => [],                 # Don't apply any validation rule to this parameter.
];
```


After the validation passes, the `resolve()` method is called. If any paramer doesn't pass validation, a 400 (Bad Request) response is thrown.


## Resolve

Inside your `resolve()` method, you have access to `$args` and `$body_args`, which both contains the request parameters **after validation**. 

> You can retreive the wordpress `WP_REST_Request` instance using the `static::$request` property.

Please read the official [wordpress documentation](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#return-value) for further informations about possible return values. 