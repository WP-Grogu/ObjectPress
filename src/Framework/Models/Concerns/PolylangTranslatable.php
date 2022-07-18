<?php

namespace OP\Framework\Models\Concerns;

use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;

/**
 * Polylang translation plugin support.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
trait PolylangTranslatable
{
    /**
     * Get the post language.
     *
     * @return string|null
     */
    public function getLanguageAttribute()
    {
        $taxo = $this->taxonomies
                    ->where('taxonomy', 'language')
                    ->first();

        return $taxo ? $taxo->term->slug : null;
    }
    
    /**
     * Set the post language.
     *
     * @param  string  $value
     * @return void
     */
    public function setLanguageAttribute($value)
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return;
        }

        $driver = $app->make(LanguageDriver::class);
        $driver->setPostLang($this->id, $value);
        $this->refresh();
    }
    
    /**
     * Get the post translation in the asked language.
     *
     * @param  string  $lang  The asked language.
     * @return static|null
     */
    public function translation(string $lang)
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return;
        }

        $driver = $app->make(LanguageDriver::class);

        # Get the current lang slug
        if ($lang == 'current') {
            $lang = $driver->getCurrentLang();
        }
        
        # Get the default/primary lang slug
        if ($lang == 'default') {
            $lang = $driver->getPrimaryLang();
        }

        $id = $driver->getPostIn($this->id, $lang);
        return $id ? static::find($id) : null;
    }
}
