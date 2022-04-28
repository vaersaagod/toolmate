<?php

namespace vaersaagod\toolmate\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use craft\helpers\ConfigHelper;
use vaersaagod\toolmate\ToolMate;
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
    private ?CspConfig $_csp = null;

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
     * @param bool  $safeOnly
     *
     * @throws InvalidConfigException
     */
    public function setAttributes($values, $safeOnly = true): void
    {
        if (array_key_exists('publicRoot', $values)) {
            $values['publicRoot'] = ToolMate::parseEnv($values['publicRoot'] ?? '@webroot');
        }

        if (array_key_exists('embedCacheDuration', $values)) {
            if ($values['embedCacheDuration'] === null) {
                $values['embedCacheDuration'] = Craft::$app->getConfig()->getGeneral()->cacheDuration;
            } elseif ($values['embedCacheDuration'] !== false) {
                $values['embedCacheDuration'] = ConfigHelper::durationInSeconds($values['embedCacheDuration']);
            }
        }

        if (array_key_exists('embedCacheDurationOnError', $values) && !empty($values['embedCacheDurationOnError'])) {
            $values['embedCacheDurationOnError'] = ConfigHelper::durationInSeconds($values['embedCacheDurationOnError']);
        }

        $this->setCsp($values['csp'] ?? []);
        unset($values['csp']);

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @param array $config
     *
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
