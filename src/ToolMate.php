<?php

namespace vaersaagod\toolmate;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;

use vaersaagod\toolmate\services\EmbedService;
use vaersaagod\toolmate\services\MinifyService;
use vaersaagod\toolmate\services\ToolService;
use vaersaagod\toolmate\variables\ToolMateVariable;
use vaersaagod\toolmate\twigextensions\ToolMateTwigExtension;
use vaersaagod\toolmate\models\Settings;

use yii\base\Event;

/**
 * @author    Værsågod
 * @package   PluginMate
 * @since     1.0.0
 *
 * @property  EmbedService $embed
 * @property  ToolService $tool
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class ToolMate extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ToolMate
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        // Register services
        $this->setComponents([
            'embed' => EmbedService::class,
            'tool' => ToolService::class,
            'minify' => MinifyService::class,
        ]);

        // Register tamplate variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('toolmate', ToolMateVariable::class);
                $variable->set('tool', ToolMateVariable::class);
            }
        );

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new ToolMateTwigExtension());
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}
