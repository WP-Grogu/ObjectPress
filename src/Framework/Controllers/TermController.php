<?php

namespace OP\Framework\Controllers;

use OP\Framework\Factories\ModelFactory;

abstract class TermController extends Controller
{
    /**
     * The term model. Can be any taxonomy.
     *
     * @var App\Models\Term|null
     */
    protected $term;

    /**
     * Initiate the post class.
     * Append the current term to the class.
     */
    public function __construct()
    {
        $this->term = ModelFactory::currentTerm();

        parent::__construct();
    }
}
