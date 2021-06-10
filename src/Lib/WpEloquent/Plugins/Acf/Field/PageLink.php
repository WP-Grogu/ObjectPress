<?php

namespace OP\Lib\WpEloquent\Plugins\Acf\Field;

use OP\Lib\WpEloquent\Plugins\Acf\FieldInterface;

/**
 * Class PageLink.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PageLink extends PostObject implements FieldInterface
{
    /**
     * @return string
     */
    public function get()
    {
        $page = parent::get();
        $domain = substr($page->guid, 0, strpos($page->guid, '?'));

        if (empty($page->post_name)) {
            return $page->guid;
        }

        return "{$domain}{$page->post_name}/";
    }
}
