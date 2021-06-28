<?php

namespace OP\Lib\WpEloquent\Plugins\Acf\Field;

use OP\Lib\WpEloquent\Plugins\Acf\FieldInterface;
use OP\Lib\WpEloquent\Model\Post;

/**
 * Class Text.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Text extends BasicField implements FieldInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $field
     */
    public function process($field)
    {
        $this->value = $this->fetchValue($field);
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->value ?: '';
    }
}
