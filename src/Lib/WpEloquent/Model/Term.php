<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Lib\WpEloquent\Model;
use OP\Lib\WpEloquent\Concerns\Aliases;
use OP\Lib\WpEloquent\Concerns\MetaFields;
use OP\Lib\WpEloquent\Model\Builder\TermBuilder;
use OP\Lib\WpEloquent\Concerns\AdvancedCustomFieldsTerms;

/**
 * Class Term.
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Term extends Model
{
    use Aliases;
    use MetaFields;
    use AdvancedCustomFieldsTerms;

    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected static $aliases = [
        'id' => 'term_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TermBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TermBuilder($query);
    }


    /******************************************/
    /*                                        */
    /*             Query builders             */
    /*                                        */
    /******************************************/


    /**
     * Get current term using wordpress magic function
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
    public function getThumbnailIdAttribute(): ?int
    {
        return ((int) $this->thumbnail->meta_value) ?: null;
    }

    

    /******************************************/
    /*                                        */
    /*        WordPress methods aliases       */
    /*                                        */
    /******************************************/


    /**
     * Get the post permalink
     *
     * @param bool $leavename (Optional) Whether to keep post name or page name. Default value: false
     *
     * @return string|false
     */
    public function getPermaLink()
    {
        return call_user_func('get_term_link', $this->term_id, $this->taxonomy->taxonomy);
    }

    /**
     * Get the post permalink
     *
     * @param bool $leavename (Optional) Whether to keep post name or page name. Default value: false
     *
     * @return string|false
     */
    public function permalink()
    {
        return $this->getPermaLink();
    }
}
