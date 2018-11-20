<?php declare (strict_types=1);

namespace TheFrosty\CustomLogin;

use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\AppFactory;
use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use TheFrosty\CustomLogin\Pro\Modules\WpLogin;
use TheFrosty\CustomLogin\WpMail\WpMail;

/**
 * Class ServiceProvider
 * @package TheFrosty\CustomLogin
 */
class ServiceProvider implements ServiceProviderInterface
{
    public const HTTP_FOUNDATION_REQUEST = 'http.request';
    public const WP_LOGIN = 'pro.wp_login';
    public const WP_MAIL = 'pro.wp_mail';
    public const WP_SETTINGS_API_APP = 'wp_settings_api';

    /**
     * Register services.
     * @param PimpleContainer $container Container instance.
     */
    public function register(PimpleContainer $container): void
    {
        $container[self::HTTP_FOUNDATION_REQUEST] = function () {
            return Request::createFromGlobals();
        };

        $container[self::WP_SETTINGS_API_APP] = function (): App {
            return AppFactory::createApp([
                'domain' => SLUG,
                'file' => __FILE__, // Path to WpSettingsApi file.
                'menu-slug' => SLUG,
                'menu-title' => \sprintf(
                    \esc_html_x(
                        'Custom Login %s',
                        'Menu title where %s is "PRO" if this is the pro version.',
                        'custom-login'
                    ),
                    CustomLogin::isPro() ? 'PRO' : ''
                ),
                'page-title' => \sprintf(
                    \esc_html_x(
                        'Custom Login %s Settings',
                        'Page title where %s is "PRO" if this is the pro version.',
                        'custom-login'
                    ),
                    CustomLogin::isPro() ? 'PRO' : ''
                ),
                'prefix' => CustomLogin::META_PREFIX,
                'version' => VERSION,
            ]);
        };

        if (CustomLogin::isPro()) {
            $this->registerProObjects($container);
        }
    }

    /**
     * Register our PRO services.
     * @param PimpleContainer $container Container instance.
     */
    private function registerProObjects(PimpleContainer $container): void
    {
        $container[self::WP_MAIL] = function (): WpMail {
            return new WpMail();
        };

        $container[self::WP_LOGIN] = function (ContainerInterface $container): WpLogin {
            return new WpLogin($container->get(self::HTTP_FOUNDATION_REQUEST));
        };
    }
}
