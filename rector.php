<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/vendor/dwnload/',
        __DIR__ . '/vendor/thefrosty/wp-utilities',
    ])
    ->withDowngradeSets(php74: true);
