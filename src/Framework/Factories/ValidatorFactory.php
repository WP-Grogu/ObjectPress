<?php

namespace OP\Framework\Factories;

use OP\Support\Facades\ObjectPress;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.6
 */
class ValidatorFactory
{
    /**
     * @var Factory
     */
    private $factory;


    /**
     * Construct the Validator instance.
     */
    public function __construct()
    {
        $this->factory = new Validator($this->getTranslator());
    }


    /**
     * Load the translator instance.
     *
     * @return Translator
     */
    protected function getTranslator()
    {
        return ObjectPress::app()->make(TranslatorContract::class)->getTranslator();
    }

    
    /**
     * Handle dynamic, calls to the factory instance thru class calling.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->factory->$method();

            case 1:
                return $this->factory->$method($args[0]);

            case 2:
                return $this->factory->$method($args[0], $args[1]);

            case 3:
                return $this->factory->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->factory->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this->factory, $method], $args);
        }
    }


    /**
     * Return the actual factory instance
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
