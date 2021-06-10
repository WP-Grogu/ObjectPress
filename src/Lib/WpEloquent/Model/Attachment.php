<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Lib\WpEloquent\Concerns\Aliases;

/**
 * Class Attachment
 *
 * @package OP\Lib\WpEloquent\Model
 * @author José CI <josec89@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Attachment extends Post
{
    use Aliases;

    /**
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @var array
     */
    protected $appends = [
        'title',
        'url',
        'type',
        'description',
        'caption',
        'alt',
    ];

    /**
     * @var array
     */
    protected static $aliases = [
        'title' => 'post_title',
        'url' => 'guid',
        'type' => 'post_mime_type',
        'description' => 'post_content',
        'caption' => 'post_excerpt',
        'alt' => ['meta' => '_wp_attachment_image_alt'],
    ];
}
