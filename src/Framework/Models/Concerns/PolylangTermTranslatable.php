<?php

namespace OP\Framework\Models\Concerns;

use OP\Framework\Models\Taxonomy;
use OP\Support\Facades\ObjectPress;
use Illuminate\Database\Eloquent\Builder;
use OP\Framework\Contracts\LanguageDriver;
use OP\Support\Language\Drivers\PolylangDriver;

/**
 * Polylang translation plugin support for terms.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.5
 */
trait PolylangTermTranslatable
{
    public function termTaxonomies()
    {
        return $this->belongsToMany(
            Taxonomy::class,
            $this->getConnection()->prefixTable('term_relationships'),
            'object_id',
            'term_taxonomy_id'
        );
    }

    /**
     * Filter query to take terms with a defined language code.
     */
    public function scopeHasLanguage(Builder $query)
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return $query;
        }

        return $query->whereHas(
            'termTaxonomies',
            fn ($tx) => $tx->where('taxonomy', 'term_language')
        );
    }

    /**
     * Filter query to take terms with a specified language code.
     *
     * @param string $lang The desired language. 'current', 'default' or language alpha2 code.
     */
    public function scopeLanguage(Builder $query, string $lang = 'current')
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return $query;
        }

        $driver = $app->make(LanguageDriver::class);

        if (!is_a($driver, PolylangDriver::class)) {
            return $query;
        }

        # Get the current lang slug
        if ($lang == 'current') {
            $lang = $driver->getCurrentLang();
        }

        # Get the default/primary lang slug
        if ($lang == 'default') {
            $lang = $driver->getPrimaryLang();
        }

        return $query->whereHas(
            'termTaxonomies',
            fn ($tx) => $tx
                ->where('taxonomy', 'term_language')
                ->whereHas('term', fn ($q) => $q->whereIn('slug', [$lang, 'pll_'.$lang]))
        );
    }

    /**
     * @deprecated Use language scope instead.
     */
    public function scopeLang(Builder $query, string $lang = 'current')
    {
        return $this->scopeLanguage($query, $lang);
    }

    /**
     * Get the post language.
     *
     * @return string|null
     */
    public function getLanguageAttribute()
    {
        $driver = ObjectPress::app()->make(LanguageDriver::class);

        return optional($driver)->getTermLang($this->id);
    }

    /**
     * Set the post language.
     *
     * @param  string  $value
     * @return void
     */
    public function setLanguageAttribute($value): void
    {
        $driver = ObjectPress::app()->make(LanguageDriver::class);

        optional($driver)->setTermLang($this->id, $value);

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
        if ($lang === 'current') {
            $lang = $driver->getCurrentLang();
        }

        # Get the default/primary lang slug
        if ($lang === 'default') {
            $lang = $driver->getPrimaryLang();
        }

        $id = $driver->getTermIn($this->id, $lang);

        return $id
            ? static::find($id)
            : null;
    }
}
