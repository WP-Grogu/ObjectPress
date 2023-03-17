<?php

namespace OP\Lib\WpEloquent\Model\Builder;

use Carbon\Carbon;
use OP\Lib\WpEloquent\Connection;
use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;
use OP\Lib\WpEloquent\Model\Scopes\CurrentLangScope;
use OP\Support\Language\Drivers\PolylangDriver;
use OP\Support\Language\Drivers\WPMLDriver;

/**
 * Class PostBuilder
 *
 * @package Corcel\Model\Builder
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class PostBuilder extends Builder
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
                      ->whereRaw("{$table}.element_id = {$prefix}posts.ID")
                      ->whereRaw("{$table}.element_type LIKE 'post_%'")
                      ->whereRaw("{$table}.language_code = '{$lang}'");
            });
        }
        
        # Polylang Support
        if (is_a($driver, PolylangDriver::class)) {
            // return $this->whereExists(function ($query) use ($db, $prefix, $lang) {
            //     $table = $prefix . 'term_taxonomy';
    
            //     $query->select($db->raw(1))
            //           ->from($table)
            //           ->where('taxonomy', 'post_translations')
            //           ->whereRaw("description regexp CONCAT('\"{$lang}\";i:', {$prefix}posts.ID, ';')");
            // });

            # TMP / TODO : see why " char get replaced by ` char
            return $this->whereIn('ID', $driver->postsInLang($lang));
        }
    }


    /**
     * Query without filtering by the current language.
     * Please note that this function only remove the related global scope.
     *
     * @return PostBuilder
     */
    public function allLangs()
    {
        return $this->withoutGlobalScope(CurrentLangScope::class);
    }
}
