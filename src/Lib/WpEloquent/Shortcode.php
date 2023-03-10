<?php

namespace OP\Lib\WpEloquent;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Interface Shortcode
 *
 * @package OP\Lib\WpEloquent
 * @author Junior Grossi <juniorgro@gmail.com>
 */
interface Shortcode
{
    /**
     * @param ShortcodeInterface $shortcode
     * @return string
     */
    public function render(ShortcodeInterface $shortcode);
}
