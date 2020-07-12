# Models

Models are really usefull, they are the link between your app and your database. A model represents a piece of data; It generally contains all the usefull methods for it's management. 

According to [this article](https://www.sitepoint.com/the-mvc-pattern-and-php-1/#:~:text=each%20component%20works.-,Model,component%20in%20the%20overall%20pattern.) from sitepoint, here is a definition of a model in a MVC pattern :

> The Model is the name given to the permanent storage of the data used in the overall design. It must allow access for the data to be viewed, or collected and written to, and is the bridge between the View component and the Controller component in the overall pattern.

Because wordpress relies on post types, ObjectPress models are "binded" to thoses ones.  

A custom post type model extends the base ObjectPress `OP\Framework\Models\Post` class.  For default post types, you can use or respectively extend `Page` or `Post` classes, or  `User` class for user management.

> A typical app should at least have a `User`, a `Page` and a `Post` model.  

Thoses models already include a lot of methods to manage wordpress posts.
 
!> Methods from `OP\Models\User` class differs from typical posts classes, as `user` *isn't a post type*. Please read more about User model below.



## Defining models

Define your models inside the `app/Models` folder. To be able to easily grab Models from ObjectPress Factories, you should respect the naming convention : kebab-case wordpress custom post types identifier (eg: 'post' or 'case-study') should have a to camel-case class name. For example, a `case-study` CPT should have `CaseStudy` as model class name.

> If you are not following the naming convention for any reason, you can specify your custom cpt-model binding inside the `app/Interfaces/ICpts.php` interface. [read more](README.md)

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

use OP\Framework\Models\Page as OP_Page;

class Page extends Page
{
    // methods goes here
}
```

#### ** Extending Post **

```php
<?php

namespace App\Models;

use OP\Framework\Models\Post as OP_Post;

class Post extends Post
{
    // methods goes here
}
```

<!-- tabs:end -->



## Using models

#### Basic usage

Models are a way to treat your custom post types, including default `post` & `page` post types.

The all point is to isolate specific methods, but also share the same methods short names between your different models, without struggling about logic differences.

For example, posts and users both **have metas**. However, wordpress doesn't manage their metas the exact same way, it uses different database tables, different methods and so on. That means that you would need to **use different methods and logic** when managing **post metas** or **user metas**.  

The OOP way allows us to write easily readable code, so we should call `getMeta` on both of our models, without caring about the "background" logic differences :  

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

Having a model for each piece of data allows to partionate your code :

```php
$page    = App\Models\Page::find(1);    // `page` post type 
$product = App\Models\Product::find(2); // `product` post type

// Using shared methods between posts
$page->postDate('d/m/Y');
$product->postDate('d/m/Y');

// Using model-specific methods
$page->getTemplate();
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

#### Post properties

The `WP_Post` properties (eg: 'post_title', 'post_name', ...) can be affected directly on your models, using their selector. (eg: $post->title)

> On post properties, the `post_` prefix is removed for a more eloquent code. For example $post->post_name becomes $post->name.


<!-- tabs:start -->

#### ** Retreiving **

```php
echo $post->date;

if (isset($post->parent) {
    echo Post::find($post->parent)->title;  // Echo $post parent title
}
```

#### ** Affecting **

```php

$parent = Post::find(10); // Get post with ID 10

$post->title    = 'Awesome !';
$post->parent   = $parent->id;
$post->password = 'secret :o';

$post->save();
```

<!-- tabs:end -->


!> ⚠️ Note the use of `->save()` method. Until you `save()` the post, properties *will not* be updated into you database if you change anything !  


## Models factories

Sometimes, you may have a post_id, but you don't know if it's a post, a page or another post_type. Because of that, you can't simply use `Post::find($post_id)` or `Page::find($post_id)`. In this case you can call the ModelFactory to automatically retreive your model, based on it's id :

```php
use OP\Framework\Factories\ModelFactory;

$post = ModelFactory::post($post_id);  // If $post_id is a `page` CPT, will return an instance of App\Models\Page
```

>You can also get the current post as if you were using the model method (eg: `Post::current()`)

```php
use OP\Framework\Factories\ModelFactory;

$post = ModelFactory::currentPost(); // Get the current post in WP loop, return an instance of it's model
```

## Some more methods

##### Check if a post belongs to a model, given a $post_id

```php
$belongs = Post::belongsToModel($id);
```