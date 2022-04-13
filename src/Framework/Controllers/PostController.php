<?php

namespace App\Controllers;

use OP\Lib\WpEloquent\Model\Post;
use OP\Lib\WpEloquent\Model\Contract\WpEloquentPost;

abstract class PostController extends Controller
{
    /**
     * The post model. Can be page, post, any post type.
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
        $this->post = Post::current();

        parent::__construct();
    }
}
