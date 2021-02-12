# API


## Introduction

API routes on ObjectPress are represented by classes. You must set one route per class.  
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
 * API Route : GET /wp-json/example/v1/get
 */
class GetExample extends ApiRoute
{
    /**
     * Api route namespace
     *
     * @var string
     */
    public static $namespace = 'example';


    /**
     * Api route
     *
     * @var string
     */
    public static $route = '/get';


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
        return [];
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
    public static $args = [];


    /**
     * Route Body parameters / variables
     *
     * @var array
     */
    public static $body_args = [
        'name' => [
            'required' => true,
            'type'     => 'String',
        ],
        'email' => [
            'required' => true,
            'type'     => 'Email',
        ],
        'phone' => [
            'required' => false,
            'type'     => 'Integer',
        ],
    ];

    /**
     * Resolve the API route.
     * 
     * @return mixed
     */
    public static function resolve(object $args, object $body_args) 
    {
        echo $body_args->name;
        echo $body_args->email;
        echo $body_args->phone;

        return [];
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
        'example/get' => 'App\Api\GetExample',
    ],
```


## Validation

On your API class, you can setup 2 parameters variables.  
- The `$args` property define the expected api route parameters  
- The `$body_args` property define the expected JSON body parameters  

#### Validation Rules

Setup your validation parameters inside the key value.    
All valitation parameters are optional.  

| Name  | Type  |  Description |
|:-:|:-:|---|
| 'required'  |  `boolean` |  Weither the field is mandatory or not. |
| 'type'  | `string`  |  The field type, to be validated by ObjectPress. Default to 'String'. |
| 'validate_callback'  |  `callable` | The validation callback. If specified, it will skip the type validation.   |


```php
public static $body_args = [
    'custom' => [
        'required'          => true,    // Boolean
        'validate_callback' => [        // Define a custom validation method, inside the API class
            static::class, 
            'class_method'
        ]  
    ],
    'string' => [
        'required' => false,      // Boolean
        'type'     => 'String',   // String 
    ],
    'without_validation' => [],
];
```


#### Default types

When you setup a `type`, ObjectPress will automatically validate it for you.  

| Type  | Method  |  Validation |
|:-:|:-:|---|
| 'String'  |  validateString() |  `is_string($param);` |
| 'Integer'  | validateInteger()  |  `preg_match("/^\d+$/", $param);` |
| 'Email'  |  validateEmail() | `is_string($param) && filter_var($param, FILTER_VALIDATE_EMAIL);`  |

#### Custom types

To create a custom type, create a validation method inside your API class. It must respect the camel case convention and start with `validate`. 

```php
public static $arg = [
    'parameter_key' => [
        'type' => 'Id',
    ],
];

/**
 * Validate the param as an Id type
 *
 * @return bool
 */
protected static validateId($param, $request = null, $key = null)
{
    return is_int($param) && $param > 0 && $param < 1000;
}
```

## Resolve

Inside your `resolve()` method, you have access to `$args` and `$body_args`, which both contains the request parameters **after validation**. 

!> Please note that fields that are not defined in class variables `$args` and `$body_args` **WILL NOT**  be returned in your `resolve()` method.


> You can retreive the wordpress `WP_REST_Request` instance using the `static::$request` property.

Please read the official [wordpress documentation](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#return-value) for further informations about possible return values. 