<?php

namespace OP\Framework\Controllers;

use OP\Framework\Factories\ModelFactory;
use AmphiBee\Eloquent\Model\Contract\WpEloquentPost;

abstract class PostController extends Controller
{
    /**
     * The post model. Can be any post type.
     *
     * @var WpEloquentPost|null
     */
    protected $post;

    /**
     * Initiate the post class.
     * Append the current post model to the class.
     */
    public function __construct()
    {
        $this->post = ModelFactory::currentPost();

        parent::__construct();
    }
}
