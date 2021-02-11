# Models

## Introduction

Models are really usefull, they are the interface between your app and your database. A model represents a piece of data; It generally contains all the usefull methods for it's management. 


> The Model is the name given to the permanent storage of the data used in the overall design. It must allow access for the data to be viewed, or collected and written to, and is the bridge between the View component and the Controller component in the overall pattern. — [Site point](https://www.sitepoint.com/the-mvc-pattern-and-php-1/#:~:text=each%20component%20works.-,Model,component%20in%20the%20overall%20pattern.)


Because wordpress relies on post types, ObjectPress has two types of models :   
- Wordpress-related models, which are "binded" to post types.   
- [Eloquent ORM](https://laravel.com/docs/8.x/eloquent) models, which are binded to an individual database table.    

> ObjectPress also supports Database migrations.


All your models are stored inside the `app/Models/` folder. To be able to use ObjectPress Factories, you **must** respect the following naming convention : The kebab-case wordpress CPT identifier (eg: 'post' or 'case-study') get transformed to a camel-case class name. For example, a `case-study` CPT should have `CaseStudy` as Model class name.

## Wordpress Models

A WP Custom post type Model extends the base ObjectPress `OP\Framework\Models\Post` class. ObjectPress has some pre-defined Models, for each default post type existing :

- Page
- Post
- Revision

Thoses models already include a lot of methods to manage wordpress posts. Please note that each of the listed class implements methods linked to their particular post type (for example, the `Page` model implements the `getTemplate()` method, which is only available for the `page` post type).
 
!> ObjectPress also has a `OP\Models\User` Model. As wordpress users aren't post types, the User Model differs from a typical posts model. Please read more about User model below.


### Defining a WP Post Model

<!-- tabs:start -->


#### ** Custom post type **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post;

class Example extends Post
{
    /**
     * Wordpress post type identifier, associated to the current model
     */
    public static $post_type = 'example';
}
```

#### ** With example method **

```php
<?php

namespace App\Models;

use OP\Framework\Models\PostModel;

class Example extends PostModel
{
    /**
     * Wordpress post type identifier, associated to the current model
     */
    public static $post_type = 'example';


    /**
     * 🤟This is an Example method on your model 😙
     * 
     * Get example locations (taxonomy) as name (string)
     *
     * @param  int    $limit       Maximum number of terms to get
     * @param  bool   $only_names  Set true to get only terms names, returns full term oject otherwise
     * 
     * @return array
     */
    public function locations(int $limit = 5, $only_names = true)
    {
        $values = $this->getTaxonomyTerms('locations');

        if ($limit != null && count($values) > $limit) {
            $values = array_slice($values, 0, $limit);
        }

        if (! $only_names) {
            return $values;
        }

        return array_map(function ($e) {
            return $e->name ?? '';
        }, $values);
    }
}
```

#### ** Extending Page **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Page as PageModel;

class Page extends PageModel
{
    // methods goes here
}
```

#### ** Extending Post **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post as PostModel;

class Post extends PostModel
{
    // methods goes here
}
```

<!-- tabs:end -->


### Using models

The all point of having models is to isolate specific methods, but also share common methods shared across post type, without struggling about logical differences. This way, you can have similar methods names doing specific stuffs.  

For example, posts and users **both have metas**. However, wordpress doesn't manage their metas the exact same way, it uses different database tables, different methods and so on. That means that you would need to **use different methods and logic** when managing **post metas** or **user metas**.  

With ObjectPress, you write readable code, so you can call `getMeta()` methods on both of yours models, without caring about the "background" logic :  

```php
/**
 * Get my models from database
 */
$user = App\Models\User::find(1);
$page = App\Models\Page::find(5);

/**
 * Get a single meta
 */
$page->getMeta('my-post-meta');
$user->getMeta('my-user-meta');
```

Custom post types share common methods, even if some of them has some particularities :

```php
$page    = App\Models\Page::find(1);    // `page` post type 
$product = App\Models\Product::find(2); // `product` post type

// Using shared methods between posts
$page->postDate('d/m/Y');
$product->postDate('d/m/Y');

// Using model-specific methods
$page->getTemplate();       // Returns my page template
$product->getTemplate();    // Error: the 'getTemplate' method is not defined on 'Product' model.
```

<!-- tabs:start -->

#### ** Page **

```php
use App\Models\Page;

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

#### ** Example **

```php
use App\Models\Example;

$example = new Example(); // This creates a new Example in database
$example->locations(2);   // We just created this function together !

// Change example status
$example->publish();
$example->trash();

// Manage metas 
$example->setMeta('meta_key', 'Hell yeah');
$example->getMeta('meta_key');                  // 'Hell yeah'


$permalink   = $example->permalink();
$metas       = $example->getMetas();            // or $example->metas();
$taxonomies  = $example->getTaxonomies();

$post_date = $example->postDate('d-m-Y');
```

<!-- tabs:end -->

### Post properties

The `WP_Post` properties (such as post_title, post_name, post_date..) can be retreived and affected directly on your models, using their selector. (eg: $post->title)

> Please not that the `post_` prefix is removed for a more eloquent code. For example $post->post_name becomes $post->name.


<!-- tabs:start -->

#### ** Retreiving **

```php
echo $post->date;

if ($post->parent) {
    echo Post::find($post->parent)->title;  // Echo $post parent title
}
```

#### ** Affecting **

```php

$post   = Post::find(5);    // Get post with id 5
$parent = Post::find(10);   // Get post with id 10

$post->title    = 'Awesome !';
$post->parent   = $parent->id;
$post->password = 'secret :o';

$post->save();
```

<!-- tabs:end -->


!> ⚠️ Note the call of `->save()` method. Until you call it, properties *will not* be updated into your database if you change anything !  


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
