<?php

namespace vaersaagod\toolmate\models;

use craft\base\Model;

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

    
    public $publicRoot = null;
    public $enableMinify = true;

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

        ];
    }
}
