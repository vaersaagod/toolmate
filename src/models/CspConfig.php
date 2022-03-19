<?php

namespace vaersaagod\toolmate\models;

use craft\base\Model;

/**
 * ToolMate CspConfig Model
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.2.0
 */
class CspConfig extends Model
{
    /** @var bool */
    public $enabled = false;

    /** @var bool */
    public $enabledForCp = false;

    /** @var bool */
    public $reportOnly = false;

    /**
     * @var CspDirectives|null
     * @see getDirectives()
     * @see setDirectives()
     */
    private $_directives;

    /** @inheritdoc */
    public function setAttributes($values, $safeOnly = true)
    {
        $this->setDirectives($values['directives'] ?? []);
        unset($values['directives']);
        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @param array $directives
     * @return void
     */
    public function setDirectives(array $config = [])
    {
        $this->_directives = new CspDirectives($config);
    }

    public function getDirectives(): CspDirectives
    {
        if (!$this->_directives) {
            $this->_directives = new CspDirectives();
        }
        return $this->_directives;
    }
}
