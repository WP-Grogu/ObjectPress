<?php

namespace OP\Providers;

use OP\Framework\Factories\TranslatorFactory;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\ServiceProvider;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @access   public
 * @version  2.0
 * @since    2.0
 */
class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * The booting method.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TranslatorContract::class, TranslatorFactory::class);
        $this->app->alias(TranslatorContract::class, 'translator');
    }
}
