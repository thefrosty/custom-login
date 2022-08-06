<?php
/**
 * Uninstall Custom Login
 *
 * @package CustomLogin
 * @copyright Copyright (c) 2014-2022, Austin Passy
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 * @since 4.0.0 Utilize all new methods.
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load Custom Login
include_once 'custom-login.php';

// In the off-chance the code isn't found, lets return, so we don't throw a fatal error.
if (!class_exists('TheFrosty\CustomLogin\CustomLogin')) {
    return;
}
$settings = TheFrosty\CustomLogin\CustomLogin::getSettings();

// Delete all plugin options
if (!empty($settings['sections'])) {
    foreach ($settings['sections'] as $section) {
        delete_option($section['id']);
    }
}

// Delete user meta data
$all_user_ids = get_users(['fields' => 'ID']);
foreach ($all_user_ids as $user_id) {
    delete_user_meta($user_id, 'custom_login_ignore_announcement');
}

// Delete all announcement options and transients
delete_option('custom_login_announcement_message');

// Delete tracking options
delete_option(TheFrosty\CustomLogin\WpAdmin\Tracking::OPTION_TRACKING_LAST_SEND);
delete_option(TheFrosty\CustomLogin\WpAdmin\Tracking::OPTION_HIDE_TRACKING_NOTICE);

// Cleanup Cron Events
wp_clear_scheduled_hook(TheFrosty\CustomLogin\Api\Cron::HOOK_DAILY);
wp_clear_scheduled_hook(TheFrosty\CustomLogin\Api\Cron::HOOK_WEEKLY);

// Delete version option
delete_option('custom_login_version');
