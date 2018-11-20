<?php declare (strict_types=1);
/**
 * Plugin Name: Custom Login
 * Plugin URI: https://frosty.media/plugins/custom-login
 * Description: A simple way to customize your WordPress <code>wp-login.php</code> screen! A <a href="https://frosty.media/">Frosty Media</a> plugin.
 * Version: 4.0.0
 * Author: Austin Passy
 * Author URI: https://austin.passy.co
 * Text Domain: custom-login
 * GitHub Branch: develop
 * GitHub Plugin URI: thefrosty/custom-login
 * Release Asset: true
 *
 * @copyright 2012 - 2019
 * @author Austin Passy
 * @link https://austin.passy.co/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace TheFrosty\CustomLogin;

const SLUG = 'custom-login';
const VERSION = '4.0.0';
const MIN_WP_VERSION = '5';
const MIN_PHP_VERSION = '7.1.0';

// If this file is called directly, abort.
\defined('WPINC') || die('Ah ah ah, you didn\'t say the magic word');

if (!\version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=')) {
    return \add_action('admin_notices', function () {
        \printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            \esc_html__(
                'Your version of PHP is incompatible with Custom Login v4 and can not be used.',
                'custom-login'
            )
        );
    });
} elseif (!\version_compare($GLOBALS['wp_version'], MIN_WP_VERSION, '>=')) {
    return \add_action('admin_notices', function () {
        \printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            \esc_html__(
                'Your version of WordPress is incompatible with Custom Login v4 and can not be used.',
                'custom-login'
            )
        );
    });
} elseif (!\file_exists(__DIR__ . '/vendor/autoload.php')) {
    return \add_action('admin_notices', function () {
        \printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            \esc_html__(
                'Please run `composer install` in this plugins directory. Custom Login v4 won\'t be used.',
                'custom-login'
            )
        );
    });
}

/**
 * Close the current session and terminate all scripts.
 */
function terminate() {
    session_write_close();
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';
