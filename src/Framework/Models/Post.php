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
        $parts = explode('\\', $class);

        // Check if it should be instantiated with a custom post type class
        if (end($parts) === 'Post' && isset($attributes['post_type']) && $attributes['post_type']) {
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_post_ID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'post_author');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Post::class, 'post_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Post::class, 'post_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachment()
    {
        return $this->hasMany(Post::class, 'post_parent')
            ->where('post_type', 'attachment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revision()
    {
        return $this->hasMany(Post::class, 'post_parent')
            ->where('post_type', 'revision');
    }
}
