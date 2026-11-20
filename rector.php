<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/custom-login.php',
        __DIR__ . '/functions.php',
        __DIR__ . '/src',
        __DIR__ . '/vendor/dwnload/',
        __DIR__ . '/vendor/thefrosty/wp-utilities',
    ])
    // WordPress function signatures, so Rector can downgrade named arguments passed to core functions
    // (e.g. `wp_register_style(..., ver: '1.0')`); without them those calls are silently skipped.
    ->withAutoloadPaths(array_filter([
        __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php',
    ], 'is_file'))
    ->withDowngradeSets(php74: true);
