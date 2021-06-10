<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Lib\WpEloquent\Concerns\AdvancedCustomFields;
use OP\Lib\WpEloquent\Concerns\MetaFields;
use OP\Lib\WpEloquent\Model;

/**
 * Class Term.
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Term extends Model
{
    use MetaFields;
    use AdvancedCustomFields;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
