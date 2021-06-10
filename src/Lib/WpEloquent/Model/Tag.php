<?php

namespace OP\Lib\WpEloquent\Model;

/**
 * Tag class.
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Mickael Burguet <www.rundef.com>
 */
class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'post_tag';
}
