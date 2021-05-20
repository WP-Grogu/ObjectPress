<?php

namespace OP\Framework\Factories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
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
        $this->factory = new Validator($this->loadTranslator());
    }
    
    /**
     * Load the translator instance.
     *
     * @return Translator
     */
    protected function loadTranslator()
    {
        $lang_dir   = OBJECTPRESS_ROOT_DIR . '/lang';

        $filesystem = new Filesystem();
        $loader     = new FileLoader($filesystem, $lang_dir);

        $loader->addNamespace('lang', $lang_dir);

        $loader->load('en', 'validation', 'lang');

        return new Translator($loader, 'en');
    }

    
    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->factory, $method],
            $args
        );
    }
}
