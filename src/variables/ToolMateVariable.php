<?php

namespace vaersaagod\toolmate\variables;

use yii\base\Exception;
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
     */
    public function inline(string $fileName, bool $remote = false): string|false
    {
        return ToolMate::$plugin->tool->inline($fileName, $remote);
    }

    /**
     * Stamps a file name or path with timestamp or hash.
     */
    public function stamp(string $fileName, string $mode = 'file', string $type = 'ts'): string
    {
        return ToolMate::$plugin->tool->stamp($fileName, $mode, $type);
    }

    /**
     * @throws Exception
     */
    public function setCookie(array $params, bool $secure = false): void
    {
        ToolMate::$plugin->tool->setCookie($params, $secure);
    }

    /**
     * @return mixed|string
     */
    public function getCookie(string $name, bool $secure = false)
    {
        return ToolMate::$plugin->tool->getCookie($name, $secure);
    }

    /**
     * @throws \Exception
     */
    public function getVideoEmbed(string $url, array $params = []): array
    {
        return ToolMate::$plugin->embed->getVideoEmbed($url, $params);
    }

}
