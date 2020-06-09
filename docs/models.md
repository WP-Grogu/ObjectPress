# Models

Models are really usefull, they are the link between your app and your database. A model represents a piece of data; It generally contains all the usefull methods for it's management.  

Because wordpress relies on post types, ObjectPress models are "binded" to thoses post types.  

A custom post type model extends the base ObjectPress `OP\Framework\Models\Post` class.  For default post types, you can use or respectively extend `Page` or `Post` classes, or  `User` class for user management.

> A typical app would have a `User`, a `Page` and a `Post` model.  
 
!> Methods from `OP\Models\User` class differs from typical posts classes, as `user` *isn't a post type*. Please read more about User model below.

Thoses models already include a lot of methods to manage wordpress posts.


## Defining models

Define your models inside the `app/Models` folder. To be able to easily grab Models from ObjectPress Factories, you should respect the naming convention : kebab-case wordpress custom post types should be converted to camel-case in you models. For example, a `case-study` CPT should have `CaseStudy` as model class name.

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

The all point is to isolate your methods, but also use the same methods name between your different models, without struggling about logic differences.

For example, posts and user both *have metas*. However, wordpress doesn't manage metas the exact same way, and you would need to use different methods when managing post or user metas. With ObjectPress, you can simply use the right model :

```php
$user = App\Models\User::find(1);
$page = App\Models\Page::find(1);

$page->getMeta('my-meta');
$user->getMeta('my-meta');
```

Having a model for each piece of data allows to partionate your code :

```php
$page    = App\Models\Page::find(1);    // `page` post type 
$product = App\Models\Product::find(2); // `product` post type

// Using shared methods between posts
$page->postDate();
$product->postDate();

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
$example->getMeta('meta_key');                 // 'Hell yeah'


$permalink   = $example->permalink();
$metas       = $example->getMetas();              // or $example->metas();
$taxonomies  = $example->getTaxonomies();

$post_date = $example->postDate('d-m-Y');
```

<!-- tabs:end -->

#### Post properties

The `WP_Post` properties (post_title, post_name, ...) can be changed directly on your models, using their selector. For example, to manage the post title :

> On post properties, the `post_` prefix is removed for a more eloquent code. For example $post->post_name becomes $post->name.


<!-- tabs:start -->

#### ** Retreiving **

```php
echo $post->date;

if (isset($post->parent) {
    echo Post::find($post->parent)->title;
}
```

#### ** Affecting **

```php
$post->title    = 'Awesome !';
$post->parent   = $parent_id;
$post->password = 'secret :o';

$post->save();
```

<!-- tabs:end -->


!> ⚠️ Note the use of `->save()` method. Until you `save()` the post, properties *will not* be updated into you database if you change anything !  


## Shared methods across models

You can create a global model as well, extending `PostModel`, so you can put commun methods, shared across your Models. For example, if your models all have a `location` meta, you could have a `getLocation()` method, returning this meta.  

```php

//  app/models/abstracts/PostModel.php

namespace App\Models\Abstracts;

use OP\Frameworks\Models\PostModel as BasePostModel;

abstract class PostModel extends BasePostModel
{
     public function getLocation()
     {
          return $this->getMeta('location');
     }
}

//  app/models/Example.php

use App\Models\Abstracts\PostModel;

class Example extends PostModel
{
     //
}

//  app/models/Post.php

use App\Models\Abstracts\PostModel;

class Post extends PostModel
{
     //
}



// Anywhere


$example = new Example($example_id);
$post    = new Post($post_id);

$example->getLocation();
$post->getLocation();
```


##### Check if a post belongs to a model, given a $post_id

```php
$belongs = Post::belongsToModel($id);
```