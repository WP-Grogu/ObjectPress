<?php

namespace OP\Lib\WpEloquent\Model\Builder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class TermBuilder
 *
 * @package ObjectPress
 * @author Thomas Georgel <thom.georgel@gmail.com>
 */
class TermBuilder extends Builder
{
    /**
     * @param string $name
     * @return TermBuilder
     */
    public function whereTaxonomy($name)
    {
        return $this->whereHas('taxonomy', function ($query) use ($name) {
            $query->where('taxonomy', $name);
        });
    }
}
