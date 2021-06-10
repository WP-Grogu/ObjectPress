<?php

namespace OP\Lib\WpEloquent\Concerns;

/**
 * Trait CustomTimestamps
 *
 * @package OP\Lib\WpEloquent\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait CustomTimestamps
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function setCreatedAt($value)
    {
        $gmt_field = static::CREATED_AT . '_gmt';
        $this->{$gmt_field} = $value;

        return parent::setCreatedAt($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function setUpdatedAt($value)
    {
        $gmt_field = static::UPDATED_AT . '_gmt';
        $this->{$gmt_field} = $value;

        return parent::setUpdatedAt($value);
    }
}
