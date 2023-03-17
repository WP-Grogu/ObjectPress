# Events & schedules

In the `setup.php` configuration file, you can define schedules events and intervals.

A typical use case is to index posts in a search engine, or to send emails to users : 

```php
    /*
    |--------------------------------------------------------------------------
    | App Events schedules declaration
    |--------------------------------------------------------------------------
    |
    | You can define wp schedules interval and events easily.
    | Events support function or class as event callback.
    |
    */
    'schedule' => [
        'events' => [
            [
                'interval' => 'everyThirtyMinutes',
                'class'    => App\Wordpress\Commands\IndexPosts::class,
            ],
            [
                'interval' => 'everyFiveMinutes',
                'class'    => App\Wordpress\Commands\WelcomeNewUsers::class,
                'when'     => fn() :bool => defined('WP_ENV') && WP_ENV === 'production',
            ],
        ],
        'intervals' => [
            [
                'name'     => 'everyFiveMinutes',
                'label'    => 'Every five minutes',
                'interval' => 5 * 60,
            ],
            [
                'name'     => 'everyThirtyMinutes',
                'label'    => 'Every thirty minutes',
                'interval' => 30 * 60,
            ],
        ],
    ],
```

## Defining events

## Defining intervals

// WIP