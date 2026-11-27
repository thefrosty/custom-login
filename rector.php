<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use TheFrosty\CustomLogin\Rector\DowngradeEnumCaseFetchRector;
use TheFrosty\CustomLogin\Rector\DowngradeIntersectionTypedPropertyRector;
use TheFrosty\CustomLogin\Rector\DowngradeParenthesizedNewRector;
use TheFrosty\CustomLogin\Rector\DowngradePropertyHooksRector;
use TheFrosty\CustomLogin\Rector\DowngradeTraitConstantRector;

// Custom rules for PHP 8.x syntax the core downgrade sets don't cover (see each rule).
foreach (glob(__DIR__ . '/.rector/*.php') ?: [] as $rule) {
    require_once $rule;
}

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/custom-login.php',
        __DIR__ . '/functions.php',
        __DIR__ . '/src',
        // All runtime dependencies ship in the PHP 7.4 package, not just our own packages
        // (e.g. wp-utilities requires blakvghost/php-validator and symfony/http-foundation, which need PHP 8+).
        __DIR__ . '/vendor',
    ])
    ->withSkip([
        // Build tooling, removed before packaging.
        __DIR__ . '/vendor/bin',
        __DIR__ . '/vendor/composer',
        __DIR__ . '/vendor/php-stubs',
        __DIR__ . '/vendor/rector',
        // Dependencies' own test suites are never loaded.
        '*/tests/*',
        '*/Tests/*',
    ])
    // WordPress function signatures, so Rector can downgrade named arguments passed to core functions
    // (e.g. `wp_register_style(..., ver: '1.0')`); without them those calls are silently skipped.
    ->withAutoloadPaths(array_filter([
        __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php',
    ], 'is_file'))
    ->withDowngradeSets(php74: true)
    ->withRules([
        DowngradeEnumCaseFetchRector::class,
        DowngradeIntersectionTypedPropertyRector::class,
        DowngradeParenthesizedNewRector::class,
        DowngradePropertyHooksRector::class,
        DowngradeTraitConstantRector::class,
    ]);
