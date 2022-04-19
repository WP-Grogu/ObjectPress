<?php

namespace OP\Framework\Models;

use OP\Framework\Helpers\PostHelper;
use Illuminate\Database\Eloquent\Builder;
use AmphiBee\Eloquent\Model\Contract\WpEloquentPost;
use OP\Framework\Models\Builder\PostBuilder;
use AmphiBee\Eloquent\Model\Page as PageModel;

/**
 * The page model.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class Page extends PageModel
{
    /**
     * Filter page which has template. If $template is provided, check filter pages with this template.
     *
     * @param Builder $query
     * @param string|array $template
     * @param string $operator (=, !=, in, not in..)
     * @return Builder
     */
    public function scopeHasTemplate(Builder $query, $template = null, string $operator = '=')
    {
        if ($template !== null) {
            $template = PostHelper::getFullTemplatePath($template); # build full template path from shotname
        }

        return parent::scopeHasTemplate($query, $template, $operator);
    }
    
    
    /**
     * Get the page template.
     *
     * @return string
     */
    public function getTemplateAttribute(): string
    {
        $tmpl = parent::getTemplateAttribute();

        return $tmpl ? PostHelper::getShortTemplateName($tmpl) : '';
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PostBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new PostBuilder($query);
    }
}
