<?php

namespace vaersaagod\toolmate\models;

use craft\base\Model;

/**
 * ToolMate CspDirectives Model
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.2.0
 */
class CspDirectives extends Model
{

    /** @var string[] */
    public $defaultSrc = [];

    /** @var string[] */
    public $scriptSrc = [];

    /** @var string[] */
    public $styleSrc = [];

    /** @var string[] */
    public $imgSrc = [];

    /** @var string[] */
    public $frameSrc = [];

    /** @var string[] */
    public $frameAncestors = [];

    /** @var string[] */
    public $baseUri = [];

    /** @var string[] */
    public $connectSrc = [];

    /** @var string[] */
    public $fontSrc = [];

    /** @var string[] */
    public $objectSrc = [];

    /** @var string[] */
    public $mediaSrc = [];

    /** @var string[] */
    public $sandbox = [];

    /** @var string[] */
    public $reportUri = [];

    /** @var string[] */
    public $childSrc = [];

    /** @var string[] */
    public $formAction = [];

    /** @var string[] */
    public $reportTo = [];

    /** @var string[] */
    public $workerSrc = [];

    /** @var string[] */
    public $manifestSrc = [];

    /** @var string[] */
    public $navigateTo = [];

}
