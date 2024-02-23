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
        if ($this instanceof \AmphiBee\Eloquent\Model\Post) {
            return $this->getPostLanguageInfo();
        }

        if ($this instanceof \AmphiBee\Eloquent\Model\Term) {
            return $this->getTermLanguageInfo();
        }
    }

    /**
     * Get the post language.
     *
     * @return string|null
     */
    protected function getPostLanguageInfo()
    {
        $taxo = $this->taxonomies
                    ->where('taxonomy', 'language')
                    ->first();

        return $taxo ? $taxo->term->slug : null;
    }

    /**
     * Get the term language.
     *
     * @return string|null
     */
    protected function getTermLanguageInfo()
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return;
        }

        $driver = $app->make(LanguageDriver::class);

        return $driver->getTermLang($this->id);
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

        if ($this instanceof \AmphiBee\Eloquent\Model\Post) {
            $driver->setPostLang($this->id, $value);
        }

        if ($this instanceof \AmphiBee\Eloquent\Model\Term) {
            $driver->setTermLang($this->id, $value);
        }

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
        $id = null;

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

        if ($this instanceof \AmphiBee\Eloquent\Model\Post) {
            $id = $driver->getPostIn($this->id, $lang);
        }

        if ($this instanceof \AmphiBee\Eloquent\Model\Term) {
            $id = $driver->getTermIn($this->id, $lang);
        }

        return $id
            ? static::find($id)
            : null;
    }
}
