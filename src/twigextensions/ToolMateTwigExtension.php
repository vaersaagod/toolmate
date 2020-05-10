<?php

namespace vaersaagod\toolmate\twigextensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use vaersaagod\toolmate\ToolMate;


/**
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class ToolMateTwigExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'ToolMate';
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array
     */
    public function getTokenParsers(): array
    {
        return [
            new MinifyTokenParser(),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setCookie', [$this, 'setCookie']),
            new TwigFunction('getCookie', [$this, 'getCookie']),
            new TwigFunction('getVideoEmbed', [$this, 'getVideoEmbed']),
            new TwigFunction('inline', [$this, 'inline']),
            new TwigFunction('stamp', [$this, 'stamp']),
        ];
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
     * @param string $url
     * @param array $params
     * @return mixed|string
     */
    public function getVideoEmbed($url, $params = [])
    {
        return ToolMate::$plugin->embed->getVideoEmbed($url, $params);
    }

    /**
     * @param string $fileName
     * @param bool $remote
     * @return false|string
     */
    public function inline($fileName, $remote = false)
    {
        return ToolMate::$plugin->tool->inline($fileName, $remote);
    }

    /**
     * @param $fileName
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function stamp($fileName, $mode = 'file', $type = 'ts'): string
    {
        return ToolMate::$plugin->tool->stamp($fileName, $mode, $type);
    }
}
