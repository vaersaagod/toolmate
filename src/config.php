<?php

return [

    'publicRoot' => '@webroot',
    'enableMinify' => true,
    'embedCacheDuration' => null, // null means use Craft's default `cacheDuration` setting
    'embedCacheDurationOnError' => 'PT5M',

    /*
     * HTTP Content-Security-Policy header config
     * https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
     */
    'csp' => [
        'enabled' => false,
        'enabledForCp' => false,
        'reportOnly' => false,
        'directives' => [
            // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/default-src
            'defaultSrc' => [],
            'scriptSrc' => [],
            // Sources for stylesheets
            'styleSrc' => [],
            // Sources for images
            'imgSrc' => [],
            // Sources for iframes
            'frameSrc' => [],
            // Domains that are allowed to iframe this site
            'frameAncestors' => [],
            'baseUri' => [],
            'connectSrc' => [],
            'fontSrc' => [],
            'objectSrc' => [],
            'mediaSrc' => [],
            'sandbox' => [],
            'reportUri' => [],
            'childSrc' => [],
            'formAction' => [],
            'reportTo' => [],
            'workerSrc' => [],
            'manifestSrc' => [],
            'navigateTo' => [],
        ],
    ],
];
