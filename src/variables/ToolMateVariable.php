<?php

namespace vaersaagod\toolmate\variables;

use vaersaagod\toolmate\ToolMate;

/**
 * ToolMate Variable
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class ToolMateVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Inlines local or remote file.
     *
     * @param string $fileName
     * @param bool   $remote
     *
     * @return string
     */
    public function inline($fileName, $remote = false): string
    {
        return ToolMate::$plugin->tool->inline($fileName, $remote);
    }

    /**
     * Stamps a file name or path with timestamp or hash.
     *
     * @param string $fileName
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function stamp($fileName, $mode = 'file', $type = 'ts'): string
    {
        return ToolMate::$plugin->tool->stamp($fileName, $mode, $type);
    }

    /**
     * @param array $params
     * @param bool $secure
     * @throws \yii\base\Exception
     */
    public function setCookie($params, $secure = false)
    {
        ToolMate::$plugin->tool->setCookie($params, $secure);
    }

    /**
     * @param string $name
     * @param bool $secure
     * @return mixed|string
     */
    public function getCookie($name, $secure = false)
    {
        return ToolMate::$plugin->tool->getCookie($name, $secure);
    }

    /**
     * @param $url
     * @param array $params
     * @return array
     */
    public function getVideoEmbed($url, $params = []): array
    {
        return ToolMate::$plugin->embed->getVideoEmbed($url, $params);
    }

    /**
     * @param $url
     * @return array
     */
    public function getEmbed($url): array
    {
        return ToolMate::$plugin->embed->getEmbed($url);
    }

}
