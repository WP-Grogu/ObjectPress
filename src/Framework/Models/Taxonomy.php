<?php

namespace OP\Framework\Models;

use OP\Support\Facades\Config;
use AmphiBee\Eloquent\Connection;
use OP\Framework\Factories\ModelFactory;
use OP\Framework\Models\Builder\TaxonomyBuilder;
use OP\Framework\Models\Scopes\CurrentLangScope;
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
     * @param array $attributes
     * @param null $connection
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->getTaxonomyInstance();

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
    protected function getTaxonomyInstance()
    {
        $class = ModelFactory::resolveTaxonomyClass() ?: static::class;

        return new $class();
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            (new Connection)->pdo->prefix() . 'term_relationships', # put prefix here to prevent issue
            'term_taxonomy_id',
            'object_id'
        );
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
     * Because the column is called "parent" by WP, the relationship cannot be always loaded using this name.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentModel()
    {
        return $this->parent();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent');
    }
}
