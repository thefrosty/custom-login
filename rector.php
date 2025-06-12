<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/vendor/dwnload/',
        __DIR__ . '/vendor/thefrosty/wp-utilities',
    ])
    ->withDowngradeSets(php74: true);
