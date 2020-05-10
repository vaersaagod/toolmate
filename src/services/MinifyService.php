<?php

namespace vaersaagod\toolmate\services;

use craft\base\Component;
use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;
use vaersaagod\toolmate\ToolMate;
use voku\helper\HtmlMin;

/**
 * Minify Service
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class MinifyService extends Component
{

    /**
     * @param string $content
     * @return string
     */
    public function minify($content): string
    {
        // todo : Parse DOM and minify any script or style tags separately?
        return $this->html($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function css($content): string 
    {
        $settings = ToolMate::$plugin->getSettings();
        
        if (!$settings->enableMinify) {
            return $content;
        }
        
        $minifier = new CSS();
        $minifier->add($content);
        return $minifier->minify();
    }

    /**
     * @param string $content
     * @return string
     */
    public function js($content): string 
    {
        $settings = ToolMate::$plugin->getSettings();
        
        if (!$settings->enableMinify) {
            return $content;
        }

        $minifier = new JS();
        $minifier->add($content);
        return $minifier->minify();
    }

    /**
     * @param string $content
     * @return string
     */
    public function html($content): string
    {
        $settings = ToolMate::$plugin->getSettings();
        
        if (!$settings->enableMinify) {
            return $content;
        }

        $htmlMin = new HtmlMin();
        return $htmlMin->minify($content);
    }
}
