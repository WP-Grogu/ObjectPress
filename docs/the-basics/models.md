# Models

> The Model is the name given to the permanent storage of the data used in the overall design. It must allow access for the data to be viewed, or collected and written to, and is the bridge between the View component and the Controller component in the overall pattern. — [Site point](https://www.sitepoint.com/the-mvc-pattern-and-php-1/#:~:text=each%20component%20works.-,Model,component%20in%20the%20overall%20pattern.)

## Introduction

To issue database select queries and insert new records, ObjectPress uses the [Eloquent ORM](https://laravel.com/docs/8.x/eloquent). Eloquent provides a beautiful, simple ActiveRecord implementation for working with your database. Each database table has a corresponding "Model" which is used to interact with that table. Models allow you to query for data in your tables, as well as insert new records into the table.

Unlike a typical Laravel project, Wordpress comes with existing database tables, and so ObjectPress already defines the correspoding models, backed-up by the [AmphiBee/wordpress-eloquent-models](https://github.com/AmphiBee/wordpress-eloquent-models) package.

You are free to extend those models to add your own methods, and you can even create your own models to interact with your own database tables.

Because Wordpress relies on post types, ObjectPress has two distincts types of models :   
- Post-related models, which are "binded" to post types (to filter posts table automatically).   
- Generic Eloquent models, which are binded to an individual database table.    

All your models are defined inside the `app/Models/` directory. To use ObjectPress Model Factories, **you must respect** the following naming convention : The kebab-case Wordpress post type identifier is converted to a camel-case class name. For example, a `case-study` post type should have a `CaseStudy` Model class name.  

## Post-related Models

A Post type Model extends the base ObjectPress `OP\Framework\Models\Post` class. ObjectPress has some pre-defined Models, for each default post type existing in Wordpress :

- Post
- Page
- Revision

Thoses models already includes some methods to manage Wordpress posts. Please also note that some of them implements specific methods related to their type (for example, the `Page` model implements the `getTemplateAttribute` method, which is only available for the `page` post type).


### Defining a Post Model

<!-- tabs:start -->


#### ** Minimal **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post;

class Event extends Post
{
    /**
     * Wordpress post type identifier, associated to the current model
     */
    public static $post_type = 'event';
}
```

#### ** Extended **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post;

class Event extends Post
{
    /**
     * Wordpress post type identifier, associated to the current model
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

    /**
     * Get the Event dates (stored as ACF properties).
     * This will be called when getting the `dates` attribute. eg: $event->dates
     * 
     * @return array
     */
    public function getDatesAttribute(): array
    {
        return $this->getField('dates') ?: [];
    }
}
```

#### ** Extend Page Model **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Page as PageModel;

class Page extends PageModel
{
    //
}
```

#### ** Extend Post Model **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post as PostModel;

class Post extends PostModel
{
    //
}
```

<!-- tabs:end -->


### Using the Post model

Post model shares the same methods as the generic Eloquent models, but also includes some methods to manage Wordpress posts.

Here are some examples of how to use the Post model :

```php
use App\Models\Page;
use App\Models\Event;

$event = new Event();  // This creates a new empty Event model, not stored yet in database

$event->title   = 'My event title';
$event->content = 'My event content';
$event->status  = 'publish',

$event->save(); // save the model in database

# Or, create and store a new Event in one line : 
Event::create([
    'title' => 'My event title',
    'content' => 'My event content',
    'status' => 'publish',
]);

// Change event status
$event->publish();
$event->trash();

// Manage metas 
$event->setMeta('meta_key', 'Hell yeah');
$event->getMeta('meta_key');                  // 'Hell yeah'


$permalink   = $event->permalink; // the event front url
$metas       = $event->meta; // the event related meta models from database
$metas       = $event->meta->pluck('meta_value', 'meta_key'); // meta as key/value collection
$taxonomies  = $event->taxonomies; // the event related taxonomy models from database

$post_date = $event->created_at->format('d-m-Y');

$page = Page::find($page_id); // Or Page::current(); for current page

$page->setTaxonomyTerms('taxonomy-name', [
	100,
	101,
]);

// Manage thumbnails 
$page->getThumbnailUrl();
$page->getThumbnailID();

$page->setThumbailFromUrl("https://images.com/my-post-image.png");


// Manage ACF fields
$page->getField('field_key');
$page->setField('field_key', 'new_value');

$page->getFields(); // all of them !
```

## Eloquent Models

Eloquent models are a way to manage custom table models, and are very usefull to query data from the database. Please refer to the [offical documentation](https://laravel.com/docs/6.x/eloquent) to have a listing of available methods and get to know the relationships system.

### Defining an Eloquent Model

```php
<?php

namespace App\Models;

use OP\Framework\Models\EloquentModel;

class Example extends EloquentModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'example_table';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];


    /**
     * The attributes that are protected from mass assignation.
     *
     * @var array
     */
    protected $protected = [];
}
```

You can now use the Eloquent Query builder :  

```php
$examples = Example::where('votes', '>', 1)->get();
```

> You can manage your database structure and migrations via ObjectPress, please refer to the [database documentation](digging-deeper/database.md).


## Models factories

Sometimes, you may have a post_id, but you don't know if it's a post, a page or another post_type. Because of that, you can't simply use `Post::find()` or `Page::find()`. In this case, you can call the ModelFactory to automatically retreive your model, based on it's id.  

You can also get the current post, as if you were using the model method (eg: `Post::current()`)

```php
use OP\Framework\Factories\ModelFactory;

$post    = ModelFactory::post($post_id);  // If post_id is a `page`, will return an instance of App\Models\Page
$current = ModelFactory::currentPost();   // Get the current post in WP loop, return an instance of it's model
```
