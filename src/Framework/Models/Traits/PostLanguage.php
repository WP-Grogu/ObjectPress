<?php

namespace OP\Framework\Models\Traits;

use OP\Framework\Helpers\LanguageHelper;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
trait PostLanguage
{
    /**
     * Get post current language
     *
     * @return string
     * @since 0.1
     */
    public function lang(): string
    {
        return $this->getLang();
    }


    /**
     * Get post current language
     *
     * @return string Lang slug (eg: 'en')
     * @since 0.1
     */
    public function getLang(): string
    {
        return LanguageHelper::getPostLang($this->id);
    }


    /**
     * Set post current language
     *
     * @param string $lang As slug (eg: 'en' or 'fr')
     *
     * @return self
     * @chainable
     * @since 0.1
     */
    public function setLang(string $lang)
    {
        LanguageHelper::setPostLang($this->id, $lang);
        return $this;
    }
}
