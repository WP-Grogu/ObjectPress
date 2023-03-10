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
     * @param string $status
     * @return PostBuilder
     */
    public function status($status)
    {
        return $this->where('post_status', $status);
    }

    /**
     * @return PostBuilder
     */
    public function published()
    {
        return $this->where(function ($query) {
            $query->status('publish');
            $query->orWhere(function ($query) {
                $query->status('future');
                $query->where('post_date', '<=', Carbon::now()->format('Y-m-d H:i:s'));
            });
        });
    }

    /**
     * @param string $type
     * @return PostBuilder
     */
    public function type($type)
    {
        return $this->where('post_type', $type);
    }

    /**
     * @param array $types
     * @return PostBuilder
     */
    public function typeIn(array $types)
    {
        return $this->whereIn('post_type', $types);
    }

    /**
     * @param string $slug
     * @return PostBuilder
     */
    public function slug($slug)
    {
        return $this->where('post_name', $slug);
    }
    
    /**
     * @param string $postParentId
     * @return PostBuilder
     */
    public function parent($postParentId)
    {
        return $this->where('post_parent', $postParentId);
    }

    /**
     * @param string $taxonomy
     * @param mixed $terms
     * @return PostBuilder
     */
    public function taxonomy($taxonomy, $terms)
    {
        return $this->whereHas('taxonomies', function ($query) use ($taxonomy, $terms) {
            $query->where('taxonomy', $taxonomy)
                ->whereHas('term', function ($query) use ($terms) {
                    $query->whereIn('slug', is_array($terms) ? $terms : [$terms]);
                });
        });
    }

    /**
     * @param mixed $term
     * @return PostBuilder
     */
    public function search($term = false)
    {
        if (empty($term)) {
            return $this;
        }

        $terms = is_string($term) ? explode(' ', $term) : $term;
        
        $terms = collect($terms)->map(function ($term) {
            return trim(str_replace('%', '', $term));
        })->filter()->map(function ($term) {
            return '%' . $term . '%';
        });

        if ($terms->isEmpty()) {
            return $this;
        }

        return $this->where(function ($query) use ($terms) {
            $terms->each(function ($term) use ($query) {
                $query->orWhere('post_title', 'like', $term)
                    ->orWhere('post_excerpt', 'like', $term)
                    ->orWhere('post_content', 'like', $term);
            });
        });
    }

    /**
     * Fix PostgreSQL format causing error on mysql.
     *
     * TODO: read configuration to determine the Database engine /!\
     *
     * @param  string  $seed
     * @return PostBuilder
     */
    public function inRandomOrder($seed = '')
    {
        return $this->orderByRaw('RAND()');
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


    /**
     * Order the results using a custom meta key.
     *
     * eg. : $query->orderByMeta('meta_key', 'DESC')
     *
     * @param string   $meta_key
     * @param string   $order
     *
     * @return PostBuilder
     */
    public function orderByMeta(string $meta_key, string $order = 'ASC')
    {
        $db     = Connection::instance();
        $prefix = $db->getPdo()->prefix();

        return $this->select([$prefix.'posts.*', $db->raw("(select meta_value from {$prefix}postmeta where {$prefix}postmeta.meta_key = '{$meta_key}' and {$prefix}posts.ID = {$prefix}postmeta.post_id limit 1) as meta_ordering")])
                ->orderByRaw('LENGTH(meta_ordering)', 'ASC') # alphanum support, avoid this kind of sort : 1, 10, 11, 7, 8
                ->orderBy('meta_ordering', $order);
    }
}
