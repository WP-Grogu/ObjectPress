Models are really usefull, they are the link between your app and your database. A model represent a piece of data, it generally contains all the methods used to manage it.  

Because wordpress relies on post types, you need to link your models to a post type. A model extends the base ObjectPress `PostModel` (`UserModel` for users) containing global methods to manage wordpress posts.  


## Defining models

You can define your models in the `App\Models` namespace (`app/Models` folder) :

```php
<?php

namespace App\Models;

use OP\Framework\Models\PostModel;

class Example extends PostModel
{
    /**
     * Wordpress post_type associated to the current model
     */
    public static $post_type = 'example';


    /**
     * // Example method on your model
     *
     * Get example locations (taxonomy) as name (string)
     *
     * @param int $limit Maximum terms to get
     * @return array
     */
    public function locations(int $limit = 5)
    {
        $values = $this->getTaxonomyTerms('locations');

        if ($limit != null && count($values) > $limit) {
            $values = array_slice($values, 0, $limit);
        }

        return array_map(function ($e) {
            return $e->name ?? '';
        }, $values);
    }
}

```


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