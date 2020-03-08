<?php

namespace OP\Framework\Models\Traits;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
trait PostAcf
{
    /**
     * Get Post's ACF fields
     *
     * @param string $format_value weither to apply acf formating or not
     * @return mixed
     *
     * @reference https://www.advancedcustomfields.com/resources/get_fields/
     * @since 0.1
     */
    public function getFields(bool $format_value = true)
    {
        return get_fields($this->post_id, $format_value);
    }


    /**
     * Get an ACF field from a given key
     *
     * @param mixed $key Selector
     * @param bool $format_value
     * @return mixed
     *
     * @reference https://www.advancedcustomfields.com/resources/get_field/
     * @since 0.1
     */
    public function getField($key, bool $format_value = true)
    {
        return get_field($key, $this->post_id, $format_value);
    }
}
