<?php

namespace vaersaagod\toolmate\twigextensions;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use vaersaagod\toolmate\ToolMate;

/**
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.2.0
 */
class CspTwigExtension extends AbstractExtension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'ToolMate CSP';
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cspNonce', [$this, 'cspNonceFunction'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $directive The directive to create a nonce for (script-src, style-src etc)
     * @param bool $asAttribute Whether to return a full HTML attribute (i.e. nonce="...")
     * @param bool $hash Whether the nonce should be hashed. Hashed nonces survive `{% cache %}` tags, unhashed nonces do not.
     * @return string|bool
     */
    public function cspNonceFunction(string $directive, bool $asAttribute = false, bool $hash = true)
    {
        if (!ToolMate::getInstance()->getSettings()->csp->enabled) {
            return false;
        }
        try {
            $nonce = ToolMate::getInstance()->csp->createNonce($directive);
            if ($hash) {
                $nonce = Craft::$app->getSecurity()->hashData("$directive:$nonce");
            }
        } catch (\Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
            return false;
        }
        if ($asAttribute) {
            return " nonce=\"$nonce\"";
        }
        return $nonce;
    }

}
