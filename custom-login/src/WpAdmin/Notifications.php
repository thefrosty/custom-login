<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class Notifications
 *
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class Notifications implements WpHooksInterface
{
    use HooksTrait;

    public function addHooks()
    {
        add_action('admin_notices', [$this, 'show_notifications']);
        add_action('admin_init', [$this, 'notification_ignore']);
    }

    /**
     * Show global notifications if they are allowed.
     */
    public function show_notifications()
    {
        $is_cl_screen = CL_Common::is_settings_page();
        $transient_key = CL_Common::get_transient_key('announcement');
        $ignore_key = CUSTOM_LOGIN_OPTION . '_ignore_announcement';
        $old_message = get_option(CUSTOM_LOGIN_OPTION . '_announcement_message');
        $user_meta = get_user_meta(get_current_user_id(), $ignore_key, true);
        $capability = CL_Common::get_option('capability', 'general', 'manage_options');

        /**
         * delete_user_meta( get_current_user_id(), $ignore_key, 1 );
         * delete_transient( $transient_key );
         * update_option( CUSTOM_LOGIN_OPTION . '_announcement_message', '' );
         */

        // Current user can't manage options
        if (!current_user_can($capability)) {
            return;
        }

        if (!$is_cl_screen) {

            // Make sure 'Frosty_Media_Notifications' isn't activated
            if (class_exists('Frosty_Media_Notifications')) {
                return;
            }

            // Global notifications
            if ('off' === CL_Common::get_option('admin_notices', 'general', 'off')) {
                return;
            }

            // Let's not show this at all if not on out menu page. @since 3.1
            return;
        }

        $message_url = esc_url(add_query_arg(['get_notifications' => 'true'], CUSTOM_LOGIN_API_URL));

        $announcement = CL_Common::wp_remote_get(
            $message_url,
            $transient_key,
            DAY_IN_SECONDS,
            'WordPress' // We need our custom $user_agent
        );

        // Bail if errors
        if (is_wp_error($announcement)) {
            return;
        }

        // Bail if false or empty
        if (!$announcement || empty($announcement[0])) {
            return;
        }

        if (trim($old_message) !== trim($announcement[0]->message) && !empty($old_message)) {
            delete_user_meta(get_current_user_id(), $ignore_key);
            delete_transient($transient_key);
            update_option(CUSTOM_LOGIN_OPTION . '_announcement_message', $announcement[0]->message);
        }

        $html = '<div class="updated"><p>';
        $html .= !$is_cl_screen ? // If we're on our settings page let not show the dismiss notice link.
            sprintf('%2$s <span class="alignright">| <a href="%3$s">%1$s</a></span>',
                __('Dismiss', CUSTOM_LOGIN_DIRNAME),
                $announcement[0]->message,
                esc_url(add_query_arg($ignore_key, wp_create_nonce($ignore_key),
                    admin_url('options-general.php?page=custom-login'))),
                esc_url(admin_url('options-general.php?page=custom-login#custom_login_general'))
            ) :
            sprintf('%s', $announcement[0]->message);
        $html .= '</p></div>';

        if ((!$user_meta && 1 !== $user_meta) || $is_cl_screen) {
            echo $html;
        }
    }

    /**
     * Remove the admin notification.
     */
    public function notification_ignore()
    {
        $ignore_key = CUSTOM_LOGIN_OPTION . '_ignore_announcement';

        // Bail if not set
        if (!isset($_GET[$ignore_key])) {
            return;
        }

        // Check nonce
        check_admin_referer($ignore_key, $ignore_key);

        // If user clicks to ignore the notice, add that to their user meta
        add_user_meta(get_current_user_id(), $ignore_key, 1, true);
    }
}
