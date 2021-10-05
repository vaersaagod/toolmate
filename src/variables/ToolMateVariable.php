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
     * @param bool $remote
     * @return false|string
     */
    public function inline(string $fileName, bool $remote = false)
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
    public function stamp(string $fileName, string $mode = 'file', string $type = 'ts'): string
    {
        return ToolMate::$plugin->tool->stamp($fileName, $mode, $type);
    }

    /**
     * @param array $params
     * @param bool $secure
     * @throws \yii\base\Exception
     */
    public function setCookie(array $params, bool $secure = false): void
    {
        ToolMate::$plugin->tool->setCookie($params, $secure);
    }

    /**
     * @param string $name
     * @param bool $secure
     * @return mixed|string
     */
    public function getCookie(string $name, bool $secure = false)
    {
        return ToolMate::$plugin->tool->getCookie($name, $secure);
    }

    /**
     * @param string $url
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getVideoEmbed(string $url, array $params = []): array
    {
        return ToolMate::$plugin->embed->getVideoEmbed($url, $params);
    }

}
