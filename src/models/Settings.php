<?php

namespace vaersaagod\toolmate\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use craft\helpers\ConfigHelper;

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
    public $publicRoot = '@webroot';

    /** @var bool */
    public $enableMinify = true;

    /** @var int|string|bool|null */
    public $embedCacheDuration = null;

    /** @var int|string|bool|null */
    public $embedCacheDurationOnError = 300;

    /**
     * @var CspConfig|null
     * @see getCsp()
     * @see setCsp()
     */
    private $_csp;

    /**
     * @param array $values
     * @param bool $safeOnly
     * @throws \yii\base\InvalidConfigException
     */
    public function setAttributes($values, $safeOnly = true)
    {

        $values['publicRoot'] = App::parseEnv($values['publicRoot'] ?? '@webroot');

        if ($values['embedCacheDuration'] === null) {
            $values['embedCacheDuration'] = Craft::$app->getConfig()->getGeneral()->cacheDuration;
        } else if ($values['embedCacheDuration'] !== false) {
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
    public function setCsp(array $config = [])
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
