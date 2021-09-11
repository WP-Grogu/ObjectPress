<?php

namespace OP\Lib\WpEloquent\Model\Builder;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;

/**
 * Class Builder
 *
 * @package ObjectPress
 * @author Thomas Georgel <thomas@hydrat.agency>
 */
class Builder extends BaseBuilder
{
    /**
     * Query without filtering by the current language.
     * Please note that this function only remove the related global scope.
     *
     * @return PostBuilder
     */
    public function allLangs()
    {
        return $this->withoutGlobalScope(CurrentLangScope::class);
    }
}
