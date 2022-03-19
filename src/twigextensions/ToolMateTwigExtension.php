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
     * @throws \Exception
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

    /**
     * @param string $fileName
     * @param bool $remote
     * @return false|string
     */
    public function inline(string $fileName, bool $remote = false)
    {
        return ToolMate::$plugin->tool->inline($fileName, $remote);
    }

    /**
     * @param string $fileName
     * @param string $mode
     * @param string $type
     * @return string
     */
    public function stamp(string $fileName, string $mode = 'file', string $type = 'ts'): string
    {
        return ToolMate::$plugin->tool->stamp($fileName, $mode, $type);
    }
}
