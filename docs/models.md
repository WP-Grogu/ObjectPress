# Models

Models are really usefull, they are the link between your app and your database. A model represents a piece of data; It generally contains all the usefull methods for it's management.  

Because wordpress relies on post types, ObjectPress models are "binded" to thoses post types. A model extends the base ObjectPress `PostModel` class for general post/pages, and the `UserModel` for users.

Thoses models already include a lot of methods to manage wordpress posts.


## Defining models

Define your models inside the `app/Models` folder. To be able to easily grab Models from ObjectPress Factories, you should respect the naming convention : kebab-case wordpress custom post types should be converted to camel-case in you models. For example, a `case-study` CPT should have `CaseStudy` as model class name.

> If you are not following the naming convention for any reason, you can specify you cpt-model binding inside the `app/Interfaces/ICpts.php` interface. [read more](README.md)

<!-- tabs:start -->

#### ** Minimal **

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

<!-- tabs:end -->



## Using models

#### Basic usage

Models are a way to treat your custom post types, including `post` and `page` post type.

`PostModel` contains various methods allowing you to treat your posts :

```php
use App\Models\Example;

$example = new Example($example_id);

$example->locations(2);  // We just created this function together !

// You can also use the PostModel functions, of course :

$permalink   = $example->permalink();
$metas       = $example->metas();
$taxonomies  = $example->getTaxonomies();

$post_date = $example->postDate('d-m-Y');

$example->setTaxonomyTerms('taxonomy-name', [
	100,
	101,
]);

// Manage thumbnails 

$example->getThumbnailUrl();
$example->getThumbnailID();

$example->setThumbailFromUrl("https://images.com/my-post-image.png");

$example->setMeta('meta_key', $meta_value);

// Change post status 

$example->publish();
$example->trash();

// Manage ACF fields

$example->getField('field_key');
$example->setField('field_key', 'new_value');

$example->getFields(); // all of them !
```

#### Affecting WP_Post properties

The WP_Post properties (post_title, post_name, ...) can be changed directly on your models. Be carefull, the `post_`prefix is removed on models, to avoid excessive length.


![docs_img_change_post_properties](uploads/a233084caf1e121ca25f3a4ac4191d5b/docs_img_change_post_properties.png)

![docs_img_change_post_properties_results](uploads/037b89ffc45db24dcb250b54d92b2f8e/docs_img_change_post_properties_results.png)


```php
// retreiving

echo $post->date;

if (isset($post->parent) {
    echo Post::find($post->parent)->title;
}

// affecting

$post->title    = 'Awesome !';
$post->parent   = $parent_id;
$post->password = 'secret :o';

$post->save();
```

⚠️ Note the use of `->save()` method. Until you `save()` the post, properties *will not* be updated into you database !  


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