<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\WpUtilities\Plugin\DefaultServiceProvider;
use function dirname;

/**
 * Class ServiceProvider
 * Container entries for Custom Login: wp-utilities' defaults (the current request and the shared View),
 * with the View loading Custom Login's templates. Registered with `Plugin::addServiceProvider()`, so it works
 * with whichever container the plugin uses (PHP-DI by default).
 * @package TheFrosty\CustomLogin
 */
class ServiceProvider extends DefaultServiceProvider
{

    /**
     * Container ID of the shared View (kept for backwards compatibility, same as `DefaultServiceProvider::VIEW`).
     */
    public const string WP_UTILITIES_VIEW = parent::VIEW;

    /**
     * Directories the shared View loads templates from.
     * @return string[]
     */
    protected function getViewPaths(): array
    {
        return [dirname(__DIR__) . '/resources/views/'];
    }
}
