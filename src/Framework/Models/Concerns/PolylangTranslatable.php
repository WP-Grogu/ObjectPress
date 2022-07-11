<?php

namespace OP\Framework\Models\Concerns;

/**
 * Polylang translation plugin support.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
trait PolylangTranslatable
{
    public function getLanguageAttribute()
    {
        $taxo = $this->taxonomies
                    ->where('taxonomy', 'language')
                    ->first();

        return $taxo ? $taxo->term->slug : null;
    }
}
