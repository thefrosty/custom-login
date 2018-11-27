<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\Pro\CustomLoginPro;
use TheFrosty\CustomLogin\Settings\ActiveModuleSettings;
use TheFrosty\CustomLogin\Settings\MailSettings;
use TheFrosty\CustomLogin\Settings\SidebarHooks;
use TheFrosty\WpUtilities\Plugin\Container;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class CustomLogin
 * @package TheFrosty\CustomLogin
 */
final class CustomLogin implements WpHooksInterface
{
    const HOOK_PREFIX = 'custom_login/';
    const META_PREFIX = 'custom_login_';

    const LAST_LOGIN = self::META_PREFIX . 'user_last_login';
    const LAST_LOGIN_IP_META_KEY = self::LAST_LOGIN . '_ip';
    const LAST_LOGIN_TIME_META_KEY = self::LAST_LOGIN . '_time';

    const USER_EMAIL = self::META_PREFIX . 'user_email';
    const USER_EMAIL_META_KEY = self::USER_EMAIL . '_notification';

    /**
     * Plugin object passed by reference, since
     * this isn't initiated until a later hook by calling initiate().
     * @var Plugin $plugin
     */
    private $plugin;

    /**
     * CustomLogin constructor.
     * @param Plugin $plugin
     */
    public function __construct(Plugin &$plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Bootstrap all other service hooks.
     */
    public function addHooks()
    {
        /** @var Container $container */
        $container = $this->plugin->getContainer();

        if (\is_admin() || \defined('DOING_AJAX') || \defined('DOING_CRON')) {
            $this->plugin
                ->add(new WpSettingsApi($container->get(ServiceProvider::WP_SETTINGS_API_APP)))
                ->addOnHook(
                    SidebarHooks::class, App::ACTION_PREFIX . 'settings_page_loaded',
                    10,
                    null,
                    [$container->get(ServiceProvider::WP_SETTINGS_API_APP)]
                )
                ->initialize();
        }

        if (self::isPro()) {
            $this->plugin
                ->add(new MailSettings())
                ->add(new ActiveModuleSettings())
                ->add(new CustomLoginPro($this->plugin))
                ->initialize();
        }
    }

    /**
     * Is this a PRO install?
     * @return bool
     */
    public static function isPro(): bool
    {
        return \file_exists(__DIR__ . '/Pro/CustomLoginPro.php');
    }
}
