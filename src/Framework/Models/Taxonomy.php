<?php

namespace OP\Framework\Models;

use OP\Support\Facades\Config;
use OP\Framework\Models\Builder\TaxonomyBuilder;
use AmphiBee\Eloquent\Model\Scopes\CurrentLangScope;
use AmphiBee\Eloquent\Model\Taxonomy as TaxonomyModel;

/**
 * The taxonomy model.
 * 
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class Taxonomy extends TaxonomyModel
{
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
     * @return TaxonomyBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new TaxonomyBuilder($query);
    }
}
