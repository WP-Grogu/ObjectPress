<?php

namespace OP\Framework\Models;

use OP\Framework\Factories\ModelFactory;
use AmphiBee\Eloquent\Model\Term as TermModel;

/**
 * The term model.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class Term extends TermModel
{
    /**
     * @param array $attributes
     * @param null $connection
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->getTermInstance((array)$attributes);

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
    protected function getTermInstance(array $attributes)
    {
        $class = ModelFactory::resolveTermClass($attributes['taxonomy'] ?? '') ?: static::class;

        return new $class();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
