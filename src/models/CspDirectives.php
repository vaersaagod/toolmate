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
    public array $defaultSrc = [];

    /** @var string[] */
    public array $scriptSrc = [];

    /** @var string[] */
    public array $styleSrc = [];

    /** @var string[] */
    public array $imgSrc = [];

    /** @var string[] */
    public array $frameSrc = [];

    /** @var string[] */
    public array $frameAncestors = [];

    /** @var string[] */
    public array $baseUri = [];

    /** @var string[] */
    public array $connectSrc = [];

    /** @var string[] */
    public array $fontSrc = [];

    /** @var string[] */
    public array $objectSrc = [];

    /** @var string[] */
    public array $mediaSrc = [];

    /** @var string[] */
    public array $sandbox = [];

    /** @var string[] */
    public array $reportUri = [];

    /** @var string[] */
    public array $childSrc = [];

    /** @var string[] */
    public array $formAction = [];

    /** @var string[] */
    public array $reportTo = [];

    /** @var string[] */
    public array $workerSrc = [];

    /** @var string[] */
    public array $manifestSrc = [];

    /** @var string[] */
    public array $navigateTo = [];
}
