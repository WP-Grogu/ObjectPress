# WP-Grogu/ObjectPress

A PHP Library to enhance Wordpress in a Object-Oriented way. Manage PostTypes, Taxonomies, Roles, Eloquent Models, WP-CLI Commands and event more, using PSR-4 classes and modern development tools.

## Documentation

You can find a detailed documentation on the [ObjectPress website](https://object-press.wp-grogu.dev/#/).

## Some feature you might like

### Create Wordpress elements

Define post types easily using PSR-4 classes. Labels are generated and translated automatically.

```php
<?php

namespace App\Wordpress\PostTypes;

use OP\Framework\Wordpress\PostType;

class Event extends PostType
{
    public static string $name = 'event';

    public $singular  = 'Event';
    public $plural    = 'Events';
    public $menu_icon = 'dashicons-store';
}
```

Same goes for taxonomies. Labels are also generated and translated automatically.

```php
<?php

namespace App\Wordpress\Taxonomies;

use OP\Framework\Wordpress\Taxonomy;

class EventType extends Taxonomy
{
    public static string $name = 'event-type';

    public $singular = 'Event type';
    public $plural   = 'Event types';

    protected $post_types = [
        'Event',
    ];
}
```

### Query your database efficiently

Your [post types](https://object-press.wp-grogu.dev/#/the-basics/custom-post-types) and [taxonomies](https://object-press.wp-grogu.dev/#/the-basics/taxonomies) are now Eloquent models. You can query your database using the same syntax as Laravel.

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post;

class Event extends Post
{
    /**
     * Wordpress post type identifier, associated to the current model.
     */
    public static $post_type = 'event';

    /**
     * Get this Event associated locations (taxonomy) names.
     *
     * @param  int    $limit  Maximum number of terms to get
     * @return array
     */
    public function locations(int $limit = 5): array
    {
        return $this->taxonomies
                    ->where('taxonomy', 'locations')
                    ->take($limit)
                    ->pluck('term.name')
                    ->toArray();
    }
}

# Get all events
$events = Event::all();

# Get all events with a specific taxonomy
$events = Event::whereHas('taxonomies', function ($query) {
    $query->where('taxonomy', 'locations')
          ->where('term.slug', 'paris');
})->get();

# Get all events with a specific taxonomy and a specific meta value
$events = Event::whereHas('taxonomies', function ($query) {
    $query->where('taxonomy', 'locations')
          ->where('term.slug', 'paris');
})->whereHas('meta', function ($query) {
    $query->where('meta_key', 'price')
          ->where('meta_value', '<', 100);
})->get();

# Get published events created in the last 30 days
$events = Event::published()
               ->where('post_date', '>', now()->subDays(30))
               ->get();
```

Read [this page](https://object-press.wp-grogu.dev/#/the-basics/models) to learn more about models models in ObjectPress. Learn more about Eloquent on the [official documentation](https://laravel.com/docs/8.x/eloquent).


### CLI Commands & Event scheduling

You can easily create [WP-CLI commands](https://object-press.wp-grogu.dev/#/the-basics/commands) using PSR-4 classes.

```php
<?php

namespace App\Wordpress\Commands;

use App\Classes\AlgoliaApi;
use OP\Framework\Wordpress\Command;

class WelcomeNewUsers extends Command
{
    /**
     * The command name called with "wp {name}"
     *
     * @var string
     * @access protected
     */
    protected string $name = 'mails:welcome-new-users';

    /**
     * Index coaches into algolia for website search.
     *
     * @param array $args The command arguments as returned by WpCLI.
     * @return void
     */
    public function execute(array $args = [])
    {
        // do something great.
    }
}
```

Define shedules in a digest configuration file : 

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

See more on the documentation.


## License
## Contributing
