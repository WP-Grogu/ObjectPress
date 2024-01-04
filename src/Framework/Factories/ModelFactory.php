<?php

namespace OP\Framework\Factories;

use Illuminate\Support\Str;
use OP\Framework\Models\Post;
use OP\Framework\Models\Term;
use OP\Framework\Models\Taxonomy;
use OP\Support\Facades\Config;
use Illuminate\Support\Collection;
use OP\Framework\Helpers\PostHelper;
use OP\Framework\Exceptions\ClassNotFoundException;
use AmphiBee\Eloquent\Model\Contract\WpEloquentPost;

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
     * The class MUST implement AmphiBee\Eloquent\Models\Contracts\WpEloquentPost interface.
     *
     * @param string $post_type The subject post type
     *
     * @return string
     * @version 2.1
     * @since 2.0
     */
    public static function resolvePostClass(string $post_type = ''): string
    {
        $psr       = Config::getFirst('object-press.theme.psr-prefix') ?: 'App';
        $conf      = Config::get('setup.models') ?: [];
        $post_type = $post_type ?: 'post';

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
                sprintf('OP\Framework\Models\%s', $post_type_camelized),
                sprintf('%s\Models\Post', $psr),
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

        return Post::class;
    }

    /**
     * Resolve the class corresponding to the asked taxonomy.
     *
     * @param string $taxonomy
     *
     * @return string
     * @version 2.1
     * @since 2.1
     */
    public static function resolveTaxonomyClass(): string
    {
        $psr = Config::getFirst('object-press.theme.psr-prefix') ?: 'App';

        $guess = [
            sprintf('%s\Models\Taxonomy', $psr),
        ];

        foreach ($guess as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return Taxonomy::class;
    }

    /**
     * Resolve the class corresponding to the asked taxonomy.
     *
     * @param string $taxonomy
     *
     * @return string
     * @version 2.1
     * @since 2.1
     */
    public static function resolveTermClass(string $taxonomy = ''): string
    {
        $psr      = Config::getFirst('object-press.theme.psr-prefix') ?: 'App';
        $taxonomy = $taxonomy ?: 'term';

        // Try to guess class model name (eg: 'taxonomy_name' => 'App\Models\TaxonomyName')
        $post_type_camelized = ucfirst(Str::camel($taxonomy));

        $guess = [
            sprintf('%s\Models\%s', $psr, $post_type_camelized),
            sprintf('OP\Framework\Models\%s', $post_type_camelized),
            sprintf('%s\Models\Term', $psr, $post_type_camelized),
        ];

        foreach ($guess as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return Term::class;
    }

    /**
     * ModelFactory for 'post' types.
     * Creates and returns the corresponding Model if applicable.
     * Returns null on failure.
     *
     * @param WP_Post|int|string|array    $post   Post element. Can be ID, WP_Post or array
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

        $class = static::resolvePostClass($post->post_type);

        return $class::find($post->ID);
    }

    /**
     * Factory 'taxonomy' model, get the taxonomy name and initiate a corresponding Model if applicable.
     *
     * @param WP_Term $term The wordpress term
     *
     * @return Term|null
     * @version 2.1
     * @since 2.1
     */
    public static function term($term)
    {
        $class = static::resolveTermClass($term->taxonomy);

        return $class::find($term->term_id);
    }

    /**
     * Call the model factory on the current post.
     *
     * @return OP\Framework\Models\Post|null on failure
     * @version 2.1
     * @since 1.0.4
     */
    public static function currentPost()
    {
        global $post;

        $id = get_the_id() ?: ($post->ID ?? false);

        return $id ? static::post($id) : null;
    }


    /**
     * Call the model factory on the current term.
     *
     * @return OP\Framework\Models\Term|null
     * @version 2.1
     * @since 1.0.4
     */
    public static function currentTerm()
    {
        $term = get_queried_object();

        if (!$term || !is_a($term, 'WP_Term')) {
            return null;
        }

        return static::term($term);
    }

    /**
     * Using wordpress helpers, returns the current model queried if appliable.
     *
     * @return OP\Framework\Models\Model|null
     */
    public static function current()
    {
        if (is_home()) {
            return static::post(get_option('page_for_posts'));
        }

        if (is_single() || is_page()) {
            return static::currentPost();
        }

        if (is_tax() || is_tag() || is_category()) {
            return static::currentTerm();
        }
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
    public static function terms(iterable $posts)
    {
        $results = [];

        foreach ($posts as $post) {
            $results[] = static::term($post);
        }

        return new Collection($results);
    }
}
