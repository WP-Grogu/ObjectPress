<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Lib\WpEloquent\Model;
use OP\Lib\WpEloquent\Connection;
use OP\Lib\WpEloquent\Concerns\Aliases;
use OP\Lib\WpEloquent\Model\Meta\TermMeta;
use OP\Lib\WpEloquent\Model\Builder\TaxonomyBuilder;

/**
 * Class Taxonomy
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Taxonomy extends Model
{
    use Aliases;

    /**
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * @var array
     */
    protected $with = ['term'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected static $aliases = [
        'name' => 'taxonomy',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Taxonomy::class, 'parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            (new Connection)->pdo->prefix() . 'term_relationships',
            'term_taxonomy_id',
            'object_id'
        );
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TaxonomyBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TaxonomyBuilder($query);
    }

    /**
     * @return TaxonomyBuilder
     */
    public function newQuery()
    {
        return isset($this->taxonomy) && $this->taxonomy ?
            parent::newQuery()->where('taxonomy', $this->taxonomy) :
            parent::newQuery();
    }

    /**
     * Magic method to return the meta data like the post original fields.
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            if (isset($this->term->$key)) {
                return $this->term->$key;
            }
        }

        return parent::__get($key);
    }


    
    /******************************************/
    /*                                        */
    /*               WP methods               */
    /*                                        */
    /******************************************/


    /**
     * Get the taxonomy labels.
     *
     * @return stdObject The labels
     */
    public function getLabelsAttribute()
    {
        return (get_taxonomy($this->name))->labels;
    }



    /******************************************/
    /*                                        */
    /*             Query builders             */
    /*                                        */
    /******************************************/


    /**
     * Get current taxonomy using wordpress magic function
     *
     * @return Model null on failure
     * @since 1.0.0
     */
    public static function current()
    {
        $id = get_queried_object_id();

        if (!$id) {
            return null;
        }

        return static::find($id);
    }
}
