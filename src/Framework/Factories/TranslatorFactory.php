<?php

namespace OP\Framework\Factories;

use OP\Support\Facades\ObjectPress;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use OP\Framework\Contracts\LanguageDriver;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 */
class TranslatorFactory
{
    /**
     * @var TranslatorContract
     */
    private $translator;


    /**
     * Construct the Validator instance.
     */
    public function __construct(string $group = 'app')
    {
        $loader = $this->getFileLoader();
        $locale = $this->getLocale();

        $loader->load($locale, $group, 'lang');

        $this->translator = new Translator($loader, $locale);
    }
    

    /**
     * Returns the current language locale.
     *
     * @return string
     */
    protected function getLocale()
    {
        if (!ObjectPress::app()->bound(LanguageDriver::class)) {
            return explode('_', get_locale())[0] ?? 'en';
        }

        $driver = ObjectPress::app()->make(LanguageDriver::class);

        return $driver->getCurrentLang();
    }


    /**
     * Build the FileLoader instance.
     *
     * @return FileLoader
     */
    protected function getFileLoader(): FileLoader
    {
        $lang_dir   = OBJECTPRESS_ROOT_DIR . '/lang';

        $filesystem = new Filesystem();
        $loader     = new FileLoader($filesystem, $lang_dir);

        $loader->addNamespace('lang', $lang_dir);

        return $loader;
    }

    
    /**
     * Handle dynamic, calls to the translator instance thru class calling.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->translator->$method();

            case 1:
                return $this->translator->$method($args[0]);

            case 2:
                return $this->translator->$method($args[0], $args[1]);

            case 3:
                return $this->translator->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->translator->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this->translator, $method], $args);
        }
    }


    /**
     * Return the actual translator
     *
     * @return TranslatorContract
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
