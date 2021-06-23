<?php

namespace OP\Core;

use OP\Support\Facades\Config;
use Jenssegers\Blade\Blade as BladeCore;

final class Blade extends BladeCore
{
    /**
     * The class instance
     *
     * @access private
     */
    private static $_instance;


    /**
     * Get the singleton instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Blade();
        }

        return self::$_instance;
    }


    /**
     * Class constructor, setup blade instance.
     *
     * @return void
     */
    private function __construct()
    {
        $inputs = collect(Config::get('object-press.template.blade.inputs'));
        $output = collect(Config::get('object-press.template.blade.output'));

        $inputs = $inputs->filter()->unique()->toArray();
        $output = $output->filter()->unique()->first();

        if (!is_dir($output)) {
            mkdir($output, 0770, true);
        }

        parent::__construct($inputs, $output);
    }


    /**
     * Templates the requested file and returns the output.
     *
     * @return string
     */
    public function template(string $view, array $data = [], array $mergeData = []): string
    {
        return $this->render($view, $data, $mergeData);
    }


    /**
     * Templates the requested file and print the output.
     *
     * @return void
     */
    public function print(string $view, array $data = [], array $mergeData = []): void
    {
        echo $this->render($view, $data, $mergeData);
    }
}
