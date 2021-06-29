<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Framework\Helpers\PostHelper;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Page
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Page extends Post
{
    /**
     * @var string
     */
    protected $postType = 'page';

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeHome(Builder $query)
    {
        return $query
            ->where('ID', '=', Option::get('page_on_front'))
            ->limit(1);
    }


    /**
     * @param Builder $query
     * @param string|array $meta
     * @param mixed $value
     * @param string $operator
     * @return Builder
     */
    public function scopeHasTemplate(Builder $query, $template = null, string $operator = '=')
    {
        # Compare with asked template value
        if ($template !== null) {
            $template = PostHelper::getFullTemplatePath($template);

            return $this->scopeHasMeta($query, '_wp_page_template', $template, $operator);
        }
        
        # No template asked, Looking for pages with templates which are not 'default'
        $query = $this->scopeHasMeta($query, '_wp_page_template');
    
        return $this->scopeHasMeta($query, '_wp_page_template', 'default', '!=');
    }
    
    
    /**
     * Get the page template
     *
     * @return string
     */
    public function getTemplateAttribute(): string
    {
        $tmpl = $this->meta->_wp_page_template ?: '';

        return $tmpl ? PostHelper::getShortTemplateName($tmpl) : '';
    }
}
