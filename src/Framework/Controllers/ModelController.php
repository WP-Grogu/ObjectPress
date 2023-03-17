<?php

namespace OP\Framework\Controllers;

use OP\Framework\Factories\ModelFactory;

abstract class ModelController extends Controller
{
    /**
     * The model. Can be post, term.
     *
     * @var App\Models\Model|null
     */
    protected $model;

    /**
     * Initiate the post class.
     * Append the current model to the class.
     */
    public function __construct()
    {
        $this->model = ModelFactory::current();

        parent::__construct();
    }
}
