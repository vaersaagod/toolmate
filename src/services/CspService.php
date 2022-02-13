<?php

namespace vaersaagod\toolmate\services;

use Craft;
use craft\base\Component;

use craft\elements\User;
use craft\helpers\StringHelper;
use vaersaagod\toolmate\ToolMate;

/**
 * Csp Service
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.2.0
 */
class CspService extends Component
{

    /** @var array */
    private $nonces = [];

    /**
     * @param string $directive
     * @return string
     * @throws \Exception
     */
    public function createNonce(string $directive): string
    {
        $nonce = \bin2hex(\random_bytes(22));
        $this->nonces[$directive][] = $nonce;
        return $nonce;
    }

    /**
     * @param string $directive
     * @param string $nonce
     * @return bool
     */
    public function hasNonce(string $directive, string $nonce): bool
    {
        return \in_array($nonce, ($this->nonces[$directive] ?? []));
    }

    /**
     * @return void
     */
    public function setHeader()
    {

        $config = ToolMate::getInstance()->getSettings()->csp;

        // Get directives
        $directivesConfig = $config->getDirectives();

        // If this is a CP request, make sure some needed policies are included
        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            // If the Yii debug toolbar is visible on the front end, we unfortunately need to set the `unsafe-inline` policy for the script-src directive
            $currentUser = Craft::$app->getUser()->getIdentity();
            if ($currentUser instanceof User && $currentUser->getPreference('enableDebugToolbarForSite')) {
                $directivesConfig->scriptSrc[] = "'unsafe-inline' 'unsafe-eval'";
            }
        } else if (Craft::$app->getRequest()->getIsCpRequest()) {
            $directivesConfig->frameAncestors[] = "'self'";
            $directivesConfig->scriptSrc[] = "'unsafe-inline' 'unsafe-eval'";
        }

        // Convert directive names to kebab-case, remove duplicates, etc
        $directivesArray = $config->getDirectives()->toArray();
        $directives = \array_reduce(\array_keys($directivesArray), function (array $carry, string $field) use ($directivesArray) {
            $policy = \array_filter(\explode(' ', \implode(' ', $directivesArray[$field])));
            if (empty($policy)) {
                return $carry;
            }
            $carry[StringHelper::toKebabCase($field)] = \array_values(\array_unique($policy));
            return $carry;
        }, []);

        // Add nonces
        foreach ($this->nonces as $directive => $nonces) {
            $directives[$directive] = $directives[$directive] ?? [];
            foreach ($nonces as $nonce) {
                if (\in_array("'unsafe-inline'", $directives[$directive])) {
                    // Skip nonces for directives with unsafe-inline
                    continue;
                }
                $directives[$directive][] = "'nonce-" . $nonce . "'";
            }
        }

        // Clear nonces
        $this->nonces = [];

        $cspValues = [];
        foreach ($directives as $directive => $policies) {
            $cspValues[] = $directive . ' ' . \join(' ', $policies);
        }

        $csp = \join('; ', $cspValues) . ';';

        if ($config->reportOnly) {
            Craft::$app->getResponse()->getHeaders()->set('Content-Security-Policy-Report-Only', $csp);
            return;
        }

        Craft::$app->getResponse()->getHeaders()->set('Content-Security-Policy', $csp);

    }

}
