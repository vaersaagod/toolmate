<?php

namespace vaersaagod\toolmate;

use Craft;
use craft\base\Plugin;
use craft\helpers\App;
use craft\web\Response;
use craft\web\TemplateResponseFormatter;
use craft\web\twig\variables\CraftVariable;

use vaersaagod\toolmate\models\Settings;
use vaersaagod\toolmate\services\CspService;
use vaersaagod\toolmate\services\EmbedService;
use vaersaagod\toolmate\services\MinifyService;
use vaersaagod\toolmate\services\ToolService;
use vaersaagod\toolmate\twigextensions\CspTwigExtension;
use vaersaagod\toolmate\twigextensions\ToolMateTwigExtension;
use vaersaagod\toolmate\variables\ToolMateVariable;

use yii\base\Event;
use yii\log\FileTarget;

/**
 * @author    Værsågod
 * @package   PluginMate
 * @since     1.0.0
 *
 * @property CspService $csp
 * @property EmbedService $embed
 * @property ToolService $tool
 * @property MinifyService $minify
 * @property Settings $settings
 * @method Settings getSettings()
 */
class ToolMate extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ToolMate
     */
    public static ToolMate $plugin;

    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        // Register services
        $this->setComponents([
            'csp' => CspService::class,
            'embed' => EmbedService::class,
            'tool' => ToolService::class,
            'minify' => MinifyService::class,
        ]);

        // Register template variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('toolmate', ToolMateVariable::class);
                $variable->set('tool', ToolMateVariable::class);
            }
        );

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new ToolMateTwigExtension());
        Craft::$app->view->registerTwigExtension(new CspTwigExtension());

        // Lets use our own log file
        Craft::getLogger()->dispatcher->targets[] = new FileTarget([
            'logFile' => '@storage/logs/toolmate.log',
            'categories' => ['vaersaagod\toolmate\*'],
        ]);

        /**
         * Maybe send a Content-Security-Policy header
         */
        $this->maybeSendCspHeader();
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @return void
     */
    protected function maybeSendCspHeader(): void
    {

        $cspConfig = self::getInstance()?->getSettings()->csp;

        if (!$cspConfig->enabled) {
            return;
        }

        if (!$cspConfig->enabledForCp && Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        // Replace hashed nonces and set the CSP header for HTML responses
        Event::on(
            Response::class,
            \yii\web\Response::EVENT_AFTER_PREPARE,
            static function (Event $event) {
                $response = $event->sender;
                if (!$response instanceof Response || empty($response?->content)) {
                    return;
                }
                $validFormats = [
                    \yii\web\Response::FORMAT_RAW,
                    \yii\web\Response::FORMAT_HTML,
                ];
                if (class_exists('craft\\web\\TemplateResponseFormatter')) {
                    $validFormats[] = TemplateResponseFormatter::FORMAT;
                }
                if (!in_array($response?->format, $validFormats)) {
                    return;
                }
                // Replace hashed CSP nonces (this gets us around the template cache)
                \preg_match_all('/nonce="([^"]*)"/', $response->content, $matches);
                $hashedNonces = $matches[1] ?? [];
                $cspService = ToolMate::getInstance()->csp;
                for ($i = 0, $iMax = \count($hashedNonces); $i < $iMax; ++$i) {
                    if (!$unhashedNonce = Craft::$app->getSecurity()->validateData($hashedNonces[$i])) {
                        continue;
                    }
                    [0 => $directive, 1 => $nonce] = \explode(':', $unhashedNonce);
                    if (!$cspService->hasNonce($directive, $nonce)) {
                        $nonce = $cspService->createNonce($directive);
                    }
                    $response->content = \str_replace($matches[0][$i], "nonce=\"$nonce\"", $response->content);
                }
                ToolMate::getInstance()->csp->setHeader($response);
            }
        );
    }
    
    /**
     * @param string|null $value
     * @return bool|string|null
     */
    public static function parseEnv(?string $value): bool|string|null
    {
        if (\version_compare(Craft::$app->getVersion(), '3.7.29', '<')) {
            return Craft::parseEnv($value);
        }
        return App::parseEnv($value);
    }
}
