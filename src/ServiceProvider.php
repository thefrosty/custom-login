<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use TheFrosty\WpUtilities\Utils\View;
use function dirname;

/**
 * Class ServiceProvider
 * @package TheFrosty
 */
class ServiceProvider implements ServiceProviderInterface
{

    public const WP_UTILITIES_VIEW = 'wp_utilities.view';

    /**
     * Register services.
     * @param PimpleContainer $pimple Container instance.
     */
    public function register(PimpleContainer $pimple): void
    {
        $pimple[self::WP_UTILITIES_VIEW] = static function (): View {
            $view = new View();
            $view->addPath(dirname(__DIR__) . '/resources/views/');

            return $view;
        };
    }
}
