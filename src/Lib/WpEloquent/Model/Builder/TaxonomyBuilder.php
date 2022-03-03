<?php

namespace OP\Lib\WpEloquent\Model\Builder;

use OP\Lib\WpEloquent\Connection;
use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;

/**
 * Class TaxonomyBuilder
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 */
class TaxonomyBuilder extends Builder
{
    /**
     * @return TaxonomyBuilder
     */
    public function category()
    {
        return $this->where('taxonomy', 'category');
    }

    /**
     * @return TaxonomyBuilder
     */
    public function menu()
    {
        return $this->where('taxonomy', 'nav_menu');
    }

    /**
     * @param string $name
     * @return TaxonomyBuilder
     */
    public function name($name)
    {
        return $this->where('taxonomy', $name);
    }

    /**
     * @param string $slug
     * @return TaxonomyBuilder
     */
    public function slug($slug = null)
    {
        if (!is_null($slug) && !empty($slug)) {
            return $this->whereHas('term', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        return $this;
    }

    /**
     * @param null $slug
     * @return TaxonomyBuilder
     */
    public function term($slug = null)
    {
        return $this->slug($slug);
    }

    /**
     * Filter query by language.
     *
     * @param string $lang The requested lang. Can be 'current', 'default', or lang code (eg: 'en', 'fr', 'it'..)
     *
     * @return PostBuilder
     */
    public function lang(string $lang = 'current')
    {
        $app    = ObjectPress::app();
        $db     = Connection::instance();
        $prefix = $db->getPdo()->prefix();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return $this;
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

        # WPML Support
        if (is_a($driver, WPMLDriver::class)) {
            return $this->whereExists(function ($query) use ($db, $prefix, $lang) {
                $table = $prefix . 'icl_translations';
    
                $query->select($db->raw(1))
                      ->from($table)
                      ->whereRaw("{$table}.element_id = {$prefix}term_taxonomy.term_id")
                      ->whereRaw("{$table}.element_type LIKE 'tax_%'")
                      ->whereRaw("{$table}.language_code = '{$lang}'");
            });
        }
        
        # Polylang Support
        // TODO
    }
}
