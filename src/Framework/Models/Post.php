<?php

namespace OP\Framework\Models;

use OP\Support\Facades\Config;
use OP\Framework\Factories\ModelFactory;
use OP\Framework\Models\Builder\PostBuilder;
use AmphiBee\Eloquent\Model\Post as PostModel;
use OP\Framework\Models\Scopes\CurrentLangScope;

/**
 * The post model.
 * 
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class Post extends PostModel
{
    /**
     * @param array $attributes
     * @return object
     */
    protected function getPostInstance(array $attributes)
    {
        $class = static::class;

        // Check if it should be instantiated with a custom post type class
        if (isset($attributes['post_type']) && $attributes['post_type']) {
            # Manual Attribution
            if (isset(static::$postTypes[$attributes['post_type']])) {
                $class = static::$postTypes[$attributes['post_type']];
            }
            # Factory Attribution
            else {
                $class = ModelFactory::resolvePostClass($attributes['post_type']) ?: static::class;
            }
        }

        return new $class();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        if (Config::getFirstBool('object-press.database.global_scope_language')) {
            static::addGlobalScope(new CurrentLangScope);
        }
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PostBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new PostBuilder($query);
    }
}
