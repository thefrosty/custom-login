<?php
/**
 * Plugin Name: Custom Login
 * Plugin URI: https://frosty.media/plugins/custom-login
 * Description: A simple way to customize your WordPress <code>wp-login.php</code> screen! A <a href="https://frosty.media/">Frosty Media</a> plugin.
 * Version: 4.0
 * Author: Austin Passy
 * Author URI: https://austin.passy.co
 * Requires PHP: 7.4
 * Text Domain: custom-login
 * GitHub Plugin URI: https://github.com/thefrosty/custom-login
 * Primary Branch: develop
 * Release Asset: true
 *
 * @copyright 2012 - 2022
 * @author Austin Passy
 * @link https://austin.passy.co/
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\Settings;
use TheFrosty\CustomLogin\WpAdmin\Dashboard;
use TheFrosty\CustomLogin\WpLogin\Login;
use TheFrosty\WpUtilities\Plugin\PluginFactory;

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

$plugin = PluginFactory::create('custom-login', __FILE__);
/** Container object. @var TheFrosty\WpUtilities\Plugin\Container $container */
$container = $plugin->getContainer();
$container->register(new ServiceProvider());
$plugin
    ->addOnHook(Dashboard::class, 'load-index.php', 5, true, [Dashboard::getArgs()])
    ->addOnHook(Login::class, 'init', 2, null, [$container])
    ->addOnHook(Settings::class, 'init', 10, true, [$container])
    ->addOnHook(WpSettingsApi::class, 'init', 10, true, [Factory::getPluginSettings($plugin)]);

add_action('plugins_loaded', static function () use ($plugin): void {
    $plugin->initialize();
});

/**
 * The main function responsible for returning the one true Instance to function everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 * @deprecated 4.0.0
 */
if (!function_exists('CUSTOMLOGIN')) {
    function CUSTOMLOGIN()
    {
        _deprecated_function(__FUNCTION__, '4.0.0');
    }
}
