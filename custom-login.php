<?php
/**
 * Plugin Name: Custom Login
 * Plugin URI: https://frosty.media/plugins/custom-login
 * Description: A simple way to customize your WordPress <code>wp-login.php</code> screen! A <a href="https://frosty.media/">Frosty Media</a> plugin.
 * Version: 4.4.0.1
 * Author: Austin Passy
 * Author URI: https://austin.passy.co
 * Requires at least: 6.6
 * Tested up to: 6.8.0
 * Requires PHP: 8.1
 * Text Domain: custom-login
 * GitHub Plugin URI: https://github.com/thefrosty/custom-login
 * Primary Branch: develop
 * Release Asset: true
 *
 * @copyright 2012 - 2025
 * @author Austin Passy
 * @link https://austin.passy.co/
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\Api\Cron;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\ImportExport;
use TheFrosty\CustomLogin\Settings\Settings;
use TheFrosty\CustomLogin\WpAdmin\Dashboard;
use TheFrosty\CustomLogin\WpAdmin\Extensions;
use TheFrosty\CustomLogin\WpAdmin\SettingsUpgrades;
use TheFrosty\CustomLogin\WpAdmin\Tracking;
use TheFrosty\CustomLogin\WpLogin\Login;
use TheFrosty\WpUtilities\Plugin\PluginFactory;

/**
 * Maybe trigger an error notice "message" on the `admin_notices` action hook.
 * Uses an anonymous function which required PHP >= 5.3.
 */
add_action('admin_notices', function () {
    $message = apply_filters('custom_login_shutdown_error_message', '');
    if (!is_admin() || empty($message)) {
        return;
    }
    load_plugin_textdomain('custom-login', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    echo wp_kses_post(sprintf('<div class="error">%s</div>', wpautop($message)));
});

if (version_compare(PHP_VERSION, '8.1', '<')) {
    return add_filter('custom_login_shutdown_error_message', function () {
        return sprintf(
            esc_html__(
                'Notice: Custom Login version 4 requires PHP version >= 8.1, you are running %s, all features are currently disabled.',
                'custom-login'
            ),
            PHP_VERSION
        );
    });
} elseif (!is_readable(__DIR__ . '/vendor/autoload.php')) {
    return add_filter('custom_login_shutdown_error_message', function () {
        return esc_html__(
            'Error: Custom Login can\'t find the autoload file (if installed from GitHub, please run `composer install`), all features are currently disabled.',
            'custom-login'
        );
    });
}

require_once __DIR__ . '/vendor/autoload.php';
$plugin = PluginFactory::create('custom-login', __FILE__);
$container = $plugin->getContainer();
$container->register(new ServiceProvider());
$plugin
    ->add(new Cron())
    ->addOnHook(CustomLogin::class, 'plugins_loaded', 5)
    ->addOnHook(Dashboard::class, 'load-index.php', 5, true, [Dashboard::getArgs()])
    ->addOnHook(Extensions::class, 'init', 10, true, [$container])
    ->addOnHook(ImportExport::class, 'init', 10, true, [$container])
    ->addOnHook(Login::class, 'init', 2, null, [$container])
    ->addOnHook(Settings::class, 'init', 10, true, [$container])
    ->addOnHook(SettingsUpgrades::class, 'init', 10, null, [$container])
    ->addOnHook(Tracking::class, 'admin_init', 10, true, [$container]);

add_action('plugins_loaded', static function () use ($plugin) {
    do_action('custom_login_loaded_before_initialize', $plugin);
    $plugin->initialize();
    do_action('custom_login_loaded_after_initialize', $plugin);
});

// Defer the WpSettingsApi until after 'init' for translations issues triggered in WP >= 6.7.0.
add_action('init', static function () use ($plugin) {
    $plugin->addOnHook(WpSettingsApi::class, 'init', 5, true, [Factory::getPluginSettings($plugin)]);
}, 2);

register_activation_hook(__FILE__, static function () {
    (new CustomLogin())->activate();
});
