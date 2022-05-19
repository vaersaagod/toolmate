<?php

namespace vaersaagod\toolmate\services;

use Craft;
use craft\base\Component;

use craft\elements\User;
use craft\helpers\StringHelper;
use craft\web\Response;

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
    private array $nonces = [];

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
        return \in_array($nonce, ($this->nonces[$directive] ?? []), true);
    }

    /**
     * @param Response $response
     * @return void
     */
    public function setHeader(Response $response): void
    {

        $config = ToolMate::getInstance()?->getSettings()->csp;
        $request = Craft::$app->getRequest();

        // Get directives
        $directivesConfig = $config->getDirectives();

        if ($request->getIsSiteRequest()) {
            // If the Yii debug toolbar is visible on the front end, we unfortunately need to set the `unsafe-inline` policy for the script-src and style-src directive
            // Also include them if the Yii error page is returned (i.e. it's an error response and dev mode is enabled)
            $currentUser = Craft::$app->getUser()->getIdentity();
            if (($currentUser instanceof User && $currentUser->getPreference('enableDebugToolbarForSite')) || ($response->getStatusCode() >= 400 && Craft::$app->getConfig()->getGeneral()->devMode)) {
                $directivesConfig->scriptSrc[] = "'unsafe-inline' 'unsafe-eval'";
                $directivesConfig->styleSrc[] = "'unsafe-inline'";
            }
        } elseif ($request->getIsCpRequest()) {
            // If this is a CP request, make sure some needed policies are included
            $directivesConfig->frameAncestors[] = "'self'";
            $directivesConfig->scriptSrc[] = "'unsafe-inline' 'unsafe-eval'";
            $directivesConfig->styleSrc[] = "'unsafe-inline'";
            $directivesConfig->fontSrc[] = 'data:';
            // Stripe
            $directivesConfig->scriptSrc[] = 'https://js.stripe.com';
            $directivesConfig->frameSrc[] = 'https://js.stripe.com';
            // Make sure Craft domains are supported
            $pluginStoreService = Craft::$app->getPluginStore();
            $directivesConfig->connectSrc[] = 'https://' . parse_url($pluginStoreService->craftApiEndpoint, PHP_URL_HOST);
            $directivesConfig->connectSrc[] = 'https://' . parse_url($pluginStoreService->craftIdEndpoint, PHP_URL_HOST);
            $directivesConfig->connectSrc[] = 'https://' . parse_url($pluginStoreService->craftOauthEndpoint, PHP_URL_HOST);
            $directivesConfig->imgSrc[] = 'https://*.craft-cdn.com';
        }

        // Convert directive names to kebab-case, remove duplicates, etc
        $directivesArray = $config->getDirectives()->toArray();
        $directives = array_reduce(array_keys($directivesArray), static function (array $carry, string $field) use ($directivesArray) {
            $policies = array_filter(explode(' ', implode(' ', $directivesArray[$field])));
            if (empty($policies)) {
                return $carry;
            }
            $policies = array_values(array_unique($policies));
            // Make sure any nonces and hashes from config are removed, if the directive contains 'unsafe-inline'
            if (in_array("'unsafe-inline'", $policies)) {
                $policies = array_filter($policies, static function ($policy) {
                    return !str_starts_with($policy, "'nonce-") && !str_starts_with($policy, "'sha256-");
                });
            }
            $carry[StringHelper::toKebabCase($field)] = $policies;
            return $carry;
        }, []);
        
        // Add memoized nonces
        foreach ($this->nonces as $directive => $nonces) {
            $directives[$directive] = $directives[$directive] ?? [];
            foreach ($nonces as $nonce) {
                if (in_array("'unsafe-inline'", $directives[$directive], true)) {
                    // Skip nonces for directives with unsafe-inline
                    continue;
                }
                $directives[$directive][] = "'nonce-" . $nonce . "'";
            }
        }

        // Clear memoized nonces
        $this->nonces = [];

        $cspValues = [];
        foreach ($directives as $directive => $policies) {
            $cspValues[] = $directive . ' ' . implode(' ', $policies);
        }

        $csp = implode('; ', $cspValues) . ';';

        if ($config->reportOnly) {
            $response->getHeaders()->set('Content-Security-Policy-Report-Only', $csp);
            return;
        }

        $response->getHeaders()->set('Content-Security-Policy', $csp);
    }
}
