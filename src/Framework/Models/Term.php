<?php

namespace OP\Framework\Models;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
