<?php

namespace OP\Framework\Factories;

use Illuminate\Support\Str;
use InvalidArgumentException;
use OP\Support\Facades\Config;
use Illuminate\Support\Collection;
use OP\Framework\Helpers\PostHelper;
use OP\Framework\Exceptions\ClassNotFoundException;
use OP\Lib\WpEloquent\Models\Contracts\WpEloquentPost;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0.0
 * @access   public
 * @since    1.0.0
 */
class ModelFactory
{
    /**
     * Resolve the class corresponding to the asked post type.
     * Read `setup.models` configuration, and then tries to guess from Camelized name.
     * The class MUST implement OP\Lib\WpEloquent\Models\Contracts\WpEloquentPost interface.
     *
     * @param string $post_type The subject post type
     *
     * @return string
     * @version 2.0
     * @since 2.0
     */
    public static function resolvePostClass(string $post_type = ''): string
    {
        $psr   = collect(Config::get('object-press.theme.psr-prefix'))->first() ?: 'App';
        $conf  = Config::get('setup.models') ?: [];

        if (!$post_type) {
            $post_type = 'post';
        }

        // Search for model in configuration array.
        if (!empty($conf) && array_key_exists($post_type, $conf)) {
            $class = $conf[$post_type];

            if (class_exists($class)) {
                return $class;
            } else {
                throw new ClassNotFoundException(
                    "ObjectPress: The `$class` model does not exists for post_type `$post_type` or doesn't implements WpEloquentPost. Please checkup your setup.php configuration file."
                );
            }
        } else {
            // Try to guess class model name (eg: 'custom-post-type' => 'App\Models\CustomPostType')
            $post_type_camelized = ucfirst(Str::camel($post_type));

            $guess = [
                sprintf('%s\Models\%s', $psr, $post_type_camelized),
                sprintf('OP\Framework\Models\%s', $post_type_camelized)
            ];

            foreach ($guess as $class) {
                if (class_exists($class)) {
                    $imlp = class_implements($class);

                    if (is_array($imlp) && in_array(WpEloquentPost::class, $imlp)) {
                        return $class;
                    }
                }
            }
        }

        return '';
    }


    /**
     * ModelFactory for 'post' types.
     * Creates and returns the corresponding Model if applicable.
     * Returns null on failure.
     *
     * @param WP_post|int|string|array    $post   Post element. Can be ID, WP_Post or array
     *
     * @return Model|null on failure
     * @version 2.0
     * @since 1.0.1
     */
    public static function post($post)
    {
        if (!($post = PostHelper::getPostFromUndefined($post))) {
            return null;
        }

        $class = static::resolvePostClass($post->post_type) ?: static::resolvePostClass('post');

        return $class::find($post->ID);
    }
    
    
    /**
     * Factory 'taxonomy' model, get the taxonomy name and initiate a corresponding Model if applicable.
     *
     * @param WP_post|int|string    $post_id   ID of the concerned post
     *
     * @return Model|null on failure
     * @version 2.0
     * @since 2.0
     */
    public static function taxonomy($tax)
    {
        // TODO
    }


    /**
     * Call the model factory on the current post
     *
     * @return Model|null on failure
     * @version 1.0.4
     * @since 1.0.4
     */
    public static function currentPost()
    {
        global $post;

        $id = get_the_id();

        if (!$id) {
            $id = $post->ID ?? false;
        }

        if (!$id) {
            return null;
        }

        return static::post($id);
    }


    /**
     * Factory an iterable of 'post' model, get the post type and initiate a corresponding Model if applicable.
     *
     * @param iterable $posts WP_post|int|string of the concerned posts
     *
     * @return Collection
     * @version 2.0
     * @since 1.0.4
     */
    public static function posts(iterable $posts)
    {
        $results = [];

        foreach ($posts as $post) {
            $results[] = static::post($post);
        }
        
        return new Collection($results);
    }
    
    
    /**
     * Factory an iterable of 'taxonomy' type model, get the post type and initiate a corresponding Model if applicable.
     *
     * @param iterable $posts WP_post|int|string of the concerned posts
     *
     * @return Collection
     * @version 2.0
     * @since 2.0
     */
    public static function taxonomies(iterable $posts)
    {
        $results = [];

        foreach ($posts as $post) {
            $results[] = static::taxonomy($post);
        }
        
        return new Collection($results);
    }
}
