<?php

namespace vaersaagod\toolmate\models;

use Craft;
use craft\base\Model;
use craft\helpers\ConfigHelper;

/**
 * ToolMate Settings Model
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
    public $embedCacheDurationOnError = null;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->setAttributes($this->getAttributes(), false);
    }

    /**
     * @param array $values
     * @param bool $safeOnly
     * @throws \yii\base\InvalidConfigException
     */
    public function setAttributes($values, $safeOnly = true)
    {

        parent::setAttributes($values, $safeOnly);

        $this->publicRoot = Craft::parseEnv($this->publicRoot) ?: ($_SERVER['DOCUMENT_ROOT'] ?? '');

        if ($this->embedCacheDuration !== false) {
            if ($this->embedCacheDuration !== null) {
                $this->embedCacheDuration = ConfigHelper::durationInSeconds($this->embedCacheDuration);
            } else {
                $this->embedCacheDuration = Craft::$app->getConfig()->getGeneral()->cacheDuration;
            }
        }

        if ($this->embedCacheDurationOnError !== false) {
            if ($this->embedCacheDurationOnError !== null) {
                $this->embedCacheDurationOnError = ConfigHelper::durationInSeconds($this->embedCacheDurationOnError);
            } else {
                $this->embedCacheDurationOnError = 300; // 5 minutes
            }
        }
    }

}
