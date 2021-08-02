<?php

namespace OP\Lib\WpEloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use OP\Lib\WpEloquent\Model;
use OP\Lib\WpEloquent\Concerns\Aliases;
use OP\Framework\Factories\ModelFactory;
use OP\Lib\WpEloquent\Concerns\MetaFields;
use OP\Lib\WpEloquent\Concerns\Shortcodes;
use OP\Lib\WpEloquent\Concerns\OrderScopes;
use OP\Lib\WpEloquent\Model\Meta\ThumbnailMeta;
use OP\Lib\WpEloquent\Concerns\CustomTimestamps;
use OP\Lib\WpEloquent\Model\Builder\PostBuilder;
use OP\Lib\WpEloquent\Concerns\AdvancedCustomFields;
use OP\Lib\WpEloquent\Model\Contract\WpEloquentPost;

/**
 * Class Post
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */
class Post extends Model implements WpEloquentPost
{
    use Aliases;
    use AdvancedCustomFields;
    use MetaFields;
    use Shortcodes;
    use OrderScopes;
    use CustomTimestamps;

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $dates = ['post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $with = ['meta'];

    /**
     * @var array
     */
    protected static $postTypes = [];

    /**
     * @var array
     */
    protected $fillable = [
        'post_content',
        'post_title',
        'post_excerpt',
        'post_type',
        'to_ping',
        'pinged',
        'post_content_filtered',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'slug',
        'content',
        'type',
        'mime_type',
        'url',
        'author_id',
        'parent_id',
        'created_at',
        'updated_at',
        'excerpt',
        'status',
        'image',
        'terms',
        'main_category',
        'keywords',
        'keywords_str',
    ];

    /**
     * @var array
     */
    protected static $aliases = [
        'id'         => 'ID',
        'title'      => 'post_title',
        'content'    => 'post_content',
        'excerpt'    => 'post_excerpt',
        'slug'       => 'post_name',
        'type'       => 'post_type',
        'mime_type'  => 'post_mime_type',
        'url'        => 'guid',
        'author_id'  => 'post_author',
        'parent_id'  => 'post_parent',
        'created_at' => 'post_date',
        'updated_at' => 'post_modified',
        'status'     => 'post_status',
    ];

    /**
     * @param array $attributes
     * @param null $connection
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->getPostInstance((array)$attributes);

        $model->exists = true;

        $model->setRawAttributes((array)$attributes, true);

        $model->setConnection(
            $connection ?: $this->getConnectionName()
        );

        return $model;
    }

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
     * @param \Illuminate\Database\Query\Builder $query
     * @return PostBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new PostBuilder($query);
    }

    /**
     * @return PostBuilder
     */
    public function newQuery()
    {
        return $this->postType ?
            parent::newQuery()->type($this->postType) :
            parent::newQuery();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thumbnail_meta()
    {
        return $this->hasOne(ThumbnailMeta::class, 'post_id')
            ->where('meta_key', '_thumbnail_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxonomies()
    {
        return $this->belongsToMany(
            Taxonomy::class,
            'wp_term_relationships', // TODO: use prefix
            'object_id',
            'term_taxonomy_id'
        );
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

    /**
     * Whether the post contains the term or not.
     *
     * @param string $taxonomy
     * @param string $term
     * @return bool
     */
    public function hasTerm($taxonomy, $term)
    {
        return isset($this->terms[$taxonomy]) &&
            isset($this->terms[$taxonomy][$term]);
    }

    /**
     * @return string
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * @return string
     */
    public function getContentAttribute()
    {
        return $this->stripShortcodes($this->post_content);
    }

    /**
     * @return string
     */
    public function getExcerptAttribute()
    {
        return $this->stripShortcodes($this->post_excerpt);
    }

    /**
     * Gets the featured image if any
     * Looks in meta the _thumbnail_id field.
     *
     * @return string
     */
    public function getImageAttribute()
    {
        if ($this->thumbnail and $this->thumbnail->attachment) {
            return $this->thumbnail->attachment->guid;
        }
    }

    /**
     * Gets all the terms arranged taxonomy => terms[].
     *
     * @return array
     */
    public function getTermsAttribute()
    {
        return $this->taxonomies->groupBy(function ($taxonomy) {
            return $taxonomy->taxonomy == 'post_tag' ?
                'tag' : $taxonomy->taxonomy;
        })->map(function ($group) {
            return $group->mapWithKeys(function ($item) {
                return [$item->term->slug => $item->term->name];
            });
        })->toArray();
    }

    /**
     * Gets the first term of the first taxonomy found.
     *
     * @return string
     */
    public function getMainCategoryAttribute()
    {
        $mainCategory = 'Uncategorized';

        if (!empty($this->terms)) {
            $taxonomies = array_values($this->terms);

            if (!empty($taxonomies[0])) {
                $terms = array_values($taxonomies[0]);
                $mainCategory = $terms[0];
            }
        }

        return $mainCategory;
    }

    /**
     * Gets the keywords as array.
     *
     * @return array
     */
    public function getKeywordsAttribute()
    {
        return collect($this->terms)->map(function ($taxonomy) {
            return collect($taxonomy)->values();
        })->collapse()->toArray();
    }

    /**
     * Gets the keywords as string.
     *
     * @return string
     */
    public function getKeywordsStrAttribute()
    {
        return implode(',', (array) $this->keywords);
    }

    /**
     * @param string $name The post type slug
     * @param string $class The class to be instantiated
     */
    public static function registerPostType($name, $class)
    {
        static::$postTypes[$name] = $class;
    }

    /**
     * Clears any registered post types.
     */
    public static function clearRegisteredPostTypes()
    {
        static::$postTypes = [];
    }

    /**
     * Get the post format, like the WP get_post_format() function.
     *
     * @return bool|string
     */
    public function getFormat()
    {
        $taxonomy = $this->taxonomies()
            ->where('taxonomy', 'post_format')
            ->first();

        if ($taxonomy && $taxonomy->term) {
            return str_replace(
                'post-format-',
                '',
                $taxonomy->term->slug
            );
        }

        return false;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);

        if ($value === null && !property_exists($this, $key)) {
            return $this->meta->$key;
        }

        return $value;
    }



    /******************************************/
    /*                                        */
    /*             Query builders             */
    /*                                        */
    /******************************************/


    /**
     * Get current post using wordpress magic function
     *
     * @return Model null on failure
     * @since 1.0.0
     */
    public static function current()
    {
        global $post;

        $id = get_the_id() ?: ($post->ID ?? false);

        if (!$id) {
            return null;
        }

        return Post::find($id);
    }


    /******************************************/
    /*                                        */
    /*            Eloquent scopes             */
    /*                                        */
    /******************************************/


    /**
     * @param Builder $query
     * @param string|array $meta
     * @param mixed $value
     * @param string $operator
     * @return Builder
     */
    // public function scopeHasTaxonomy(Builder $query, $meta, $value = null, string $operator = '=')
    // {
    //     if (!is_array($meta)) {
    //         $meta = [$meta => $value];
    //     }

    //     foreach ($meta as $key => $value) {
    //         $query->whereHas('meta', function (Builder $query) use ($key, $value, $operator) {
    //             if (!is_string($key)) {
    //                 return $query->where('meta_key', $operator, $value);
    //             }
    //             $query->where('meta_key', $operator, $key);

    //             return is_null($value) ? $query :
    //                 $query->where('meta_value', $operator, $value);
    //         });
    //     }

    //     return $query;
    // }


    /**
     * Get a collection of posts from their ids, keeping the given ids ordering.
     *
     * Eg: Post::ids([10,12,11])->get()
     *
     * @param Builder $query
     * @param array   $ids
     */
    public function scopeIds(Builder $query, array $ids)
    {
        $ids_imp = implode(',', $ids);

        return $query->whereIn('ID', $ids)
                    ->orderByRaw("FIELD(ID, $ids_imp)");
    }


    /******************************************/
    /*                                        */
    /*        WordPress related methods       */
    /*                                        */
    /******************************************/


    /**
     * Automatically fetch the permalink when trying to acces this attribute.
     *
     * @return string
     */
    public function getPermalinkAttribute()
    {
        return $this->getPermaLink();
    }


    /**
     * Get the thumbnail id
     *
     * @return int|null
     */
    public function getThumbnailAttribute()
    {
        return Attachment::find(
            $this->getThumbnailIdAttribute()
        );
    }


    /**
     * Get the thumbnail id
     *
     * @return int|null
     */
    public function getThumbnailIdAttribute(): ?int
    {
        return ((int) $this->thumbnail_meta->meta_value) ?: 0;
    }
    

    /**
     * Get the thumbnail alt.
     * If null, returns the post title instead.
     *
     * @return string
     */
    public function getThumbnailAltAttribute(): string
    {
        return ($this->thumbnail->alt ?: $this->title) ?: '';
    }


    /******************************************/
    /*                                        */
    /*        WordPress methods aliases       */
    /*                                        */
    /******************************************/



    /**
     * Get the post edition link in back-office
     *
     * @return string
     */
    public function getEditionLink()
    {
        return admin_url("post.php?post={$this->id}&action=edit");
    }

    /**
     * Get the post edition link in back-office
     *
     * @return string
     */
    public function getPreviewLink()
    {
        return get_preview_post_link($this->id);
    }

    /**
     * Get the post permalink
     *
     * @param bool $leavename (Optional) Whether to keep post name or page name. Default value: false
     *
     * @return string|false
     */
    public function getPermaLink(bool $leavename = false)
    {
        return get_permalink($this->id, $leavename);
    }

    /**
     * Get the post permalink
     *
     * @param bool $leavename (Optional) Whether to keep post name or page name. Default value: false
     *
     * @return string|false
     */
    public function permalink(bool $leavename = false)
    {
        return $this->getPermaLink($leavename);
    }
}
