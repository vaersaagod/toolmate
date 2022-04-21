<?php

namespace vaersaagod\toolmate\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use craft\helpers\ConfigHelper;
use yii\base\InvalidConfigException;

/**
 * ToolMate Settings Model
 *
 * @property-read CspConfig $csp
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /** @var string */
    public string $publicRoot = '@webroot';

    /** @var bool */
    public bool $enableMinify = true;

    /** @var int|string|bool|null */
    public string|int|bool|null $embedCacheDuration = null;

    /** @var int|string|bool|null */
    public string|int|bool|null $embedCacheDurationOnError = 300;

    /**
     * @var CspConfig|null
     * @see getCsp()
     * @see setCsp()
     */
    private ?CspConfig $_csp;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->setAttributes($this->getAttributes(), false);
    }

    /**
     * @param array $values
     * @param bool $safeOnly
     * @throws InvalidConfigException
     */
    public function setAttributes($values, $safeOnly = true): void
    {
        if (version_compare(Craft::$app->getVersion(), '3.7.29', '>=')) {
            $values['publicRoot'] = App::parseEnv($values['publicRoot'] ?? '@webroot');
        } else {
            $values['publicRoot'] = Craft::parseEnv($values['publicRoot'] ?? '@webroot');
        }

        if ($values['embedCacheDuration'] === null) {
            $values['embedCacheDuration'] = Craft::$app->getConfig()->getGeneral()->cacheDuration;
        } elseif ($values['embedCacheDuration'] !== false) {
            $values['embedCacheDuration'] = ConfigHelper::durationInSeconds($values['embedCacheDuration']);
        }

        if (!empty($values['embedCacheDurationOnError'])) {
            $values['embedCacheDurationOnError'] = ConfigHelper::durationInSeconds($values['embedCacheDurationOnError']);
        }

        $this->setCsp($values['csp'] ?? []);
        unset($values['csp']);

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @param array $config
     * @return void
     */
    public function setCsp(array $config = []): void
    {
        $this->_csp = new CspConfig($config);
    }

    /**
     * @return CspConfig
     */
    public function getCsp(): CspConfig
    {
        if (!$this->_csp) {
            $this->_csp = new CspConfig();
        }
        return $this->_csp;
    }
}
