<?php

namespace OP\Framework\Models\Builder;

use AmphiBee\Eloquent\Model\Builder\TaxonomyBuilder as BaseTaxonomyBuilder;
use AmphiBee\Eloquent\Connection;
use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;

/**
 * The taxonomy model query builder.
 * 
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class TaxonomyBuilder extends BaseTaxonomyBuilder
{
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
