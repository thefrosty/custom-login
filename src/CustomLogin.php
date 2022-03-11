<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;
use TheFrosty\WpUtilities\Utils\SingletonInterface;

/**
 * Class CustomLogin
 * @package TheFrosty\CustomLogin
 */
class CustomLogin extends AbstractHookProvider implements WpHooksInterface
{

    /**
     * Plugin vars
     *
     * @return string
     */
    public $version = '3.2.14',
        $menu_page,
        $prefix;

    /**
     * Custom Login Settings API
     *
     * @var \CL_Settings_API $settings_api
     */
    public $settings_api;

    /**
     * Main Instance
     */
    public static function getInstance(): SingletonInterface
    {
        $instance->setup_constants();
        add_action('plugins_loaded', [$instance, 'plugin_textdomain']);
        $instance->includes();
        $instance->actions();


        return $instance;
    }

    public function addHooks(): void
    {
        // TODO: Implement addHooks() method.
    }

    /**
     * Setup plugin constants
     *
     * @access    private
     * @return    void
     * @since    3.0
     */
    private function setup_constants()
    {
        // API URL
        if (!defined('CUSTOM_LOGIN_API_URL')) {
            define('CUSTOM_LOGIN_API_URL', 'https://frosty.media/');
        }

        // Plugin version
        if (!defined('CUSTOM_LOGIN_VERSION')) {
            define('CUSTOM_LOGIN_VERSION', $this->version);
        }

        // Plugin Root File
        if (!defined('CUSTOM_LOGIN_FILE')) {
            define('CUSTOM_LOGIN_FILE', __FILE__);
        }

        // Plugin Folder Path
        if (!defined('CUSTOM_LOGIN_DIR')) {
            define('CUSTOM_LOGIN_DIR', plugin_dir_path(CUSTOM_LOGIN_FILE));
        }

        // Plugin Folder URL
        if (!defined('CUSTOM_LOGIN_URL')) {
            define('CUSTOM_LOGIN_URL', plugin_dir_url(CUSTOM_LOGIN_FILE));
        }

        // Plugin Root Basename
        if (!defined('CUSTOM_LOGIN_BASENAME')) {
            define('CUSTOM_LOGIN_BASENAME', plugin_basename(CUSTOM_LOGIN_FILE));
        }

        // Plugin Dirname
        if (!defined('CUSTOM_LOGIN_DIRNAME')) {
            define('CUSTOM_LOGIN_DIRNAME', dirname(CUSTOM_LOGIN_BASENAME));
        }

        // Plugin Settings Name
        if (!defined('CUSTOM_LOGIN_OPTION')) {
            define('CUSTOM_LOGIN_OPTION', str_replace('-', '_', CUSTOM_LOGIN_DIRNAME));
        }
    }

    /**
     * Load the plugin translations
     *
     */
    public function plugin_textdomain()
    {
        load_plugin_textdomain(CUSTOM_LOGIN_DIRNAME, false, CUSTOM_LOGIN_DIRNAME . '/languages/');
    }

    /**
     * Includes required functions
     *
     */
    private function includes()
    {
        require_once __DIR__ . '../includes/class-cl-common.php';
        require_once __DIR__ . '../includes/class-cl-cron.php';
        require_once __DIR__ . '../includes/class-cl-extensions.php';
        require_once __DIR__ . '../includes/class-cl-templates.php';
        require_once __DIR__ . '../includes/class-cl-scripts-styles.php';
        require_once __DIR__ . '../includes/class-cl-settings-api.php';
        require_once __DIR__ . '../includes/class-cl-settings-upgrades.php';
        require_once __DIR__ . '../includes/class-cl-wp-login.php';
        require_once __DIR__ . '../includes/functions.php';

        if (is_admin()) {
            require_once __DIR__ . '../includes/admin/dashboard.php';
            require_once __DIR__ . '../includes/admin/plugins.php';
            require_once __DIR__ . '../includes/admin/import-export.php';
            require_once __DIR__ . '../includes/admin/tracking.php';
        }
    }

    /**
     * To infinity and beyond
     */
    private function actions()
    {
        $this->prefix = CUSTOM_LOGIN_OPTION;

        register_activation_hook(CUSTOM_LOGIN_FILE, [$this, 'activate']);

        add_action('login_head', [$this, 'cl_version_in_header'], 1);
        add_action('wp_head', [$this, 'cl_version_in_header']);
        add_action('admin_menu', [$this, 'admin_menu'], 9);
        add_action('admin_init', [$this, 'load_settings'], 8);
        add_action($this->prefix . '_after_sanitize_options', [$this, 'delete_transients'], 8);

        add_action('admin_notices', [$this, 'show_notifications']);
        add_action('admin_init', [$this, 'notification_ignore']);

        do_action($this->prefix . '_actions');
    }

    /**
     * Runs on plugin install.
     *
     * @since 3.1
     */
    function activate()
    {
        $settings = \CL_Common::get_options('general');
        $settings['active'] = 'on';

        update_option(CUSTOM_LOGIN_OPTION . '_general', $settings);
    }

    /**
     * Adds CL Version to the <head> tag
     *
     * @return void
     * @since 3.0.0
     */
    function cl_version_in_header()
    {
        echo '<meta name="generator" content="Custom Login v' . CUSTOM_LOGIN_VERSION . '" />' . "\n";
    }

    /**
     * Register the plugin page
     */
    public function admin_menu()
    {
        $capability = \CL_Common::get_option('capability', 'general', 'manage_options');

        $this->menu_page = add_options_page(
            __('Custom Login Settings', CUSTOM_LOGIN_DIRNAME),
            __('Custom Login', CUSTOM_LOGIN_DIRNAME),
            $capability,
            CUSTOM_LOGIN_DIRNAME,
            [$this, 'settings_page']
        );
    }

    /**
     * Display the plugin settings options page
     */
    public function settings_page()
    { ?>
        <div class="wrap">
        <?php $this->settings_api->settings_html(); ?>
        </div><?php
    }

    /**
     * Display the plugin settings options page
     */
    public function load_settings()
    {

        $fields = $sections = [];
        include __DIR__ . '../includes/default-settings.php';
        $this->settings_api = new \CL_Settings_API(
            $sections,
            $fields,
            [
                'option_name' => CUSTOM_LOGIN_OPTION,
                'option_group' => CUSTOM_LOGIN_OPTION . '_group',
                'domain' => CUSTOM_LOGIN_DIRNAME,
                'prefix' => $this->prefix,
                'version' => $this->version,
                'menu_page' => $this->menu_page,
                'nonce' => CUSTOM_LOGIN_OPTION . '_nonce_' . CUSTOM_LOGIN_BASENAME,
                'file' => CUSTOM_LOGIN_FILE,
            ]
        );
        $this->settings_api->admin_init();
    }

    /**
     * Hook into the 'sanitize_options' hook in the Settings API
     * and remove the transient settings for the style and script.
     *
     * @since    3.0.0
     */
    public function delete_transients()
    {
        delete_transient(\CL_Common::get_transient_key('style'));
        delete_transient(\CL_Common::get_transient_key('script'));
    }

    /**
     * Show global notifications if they are allowed.
     *
     */
    public function show_notifications()
    {
        $is_cl_screen = \CL_Common::is_settings_page();
        $transient_key = \CL_Common::get_transient_key('announcement');
        $ignore_key = CUSTOM_LOGIN_OPTION . '_ignore_announcement';
        $old_message = get_option(CUSTOM_LOGIN_OPTION . '_announcement_message');
        $user_meta = get_user_meta(get_current_user_id(), $ignore_key, true);
        $capability = \CL_Common::get_option('capability', 'general', 'manage_options');

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
            if ('off' === \CL_Common::get_option('admin_notices', 'general', 'off')) {
                return;
            }

            // Let's not show this at all if not on out menu page. @since 3.1
            return;
        }

        $message_url = esc_url(add_query_arg(['get_notifications' => 'true'], CUSTOM_LOGIN_API_URL));

        $announcement = \CL_Common::wp_remote_get(
            $transient_key,
            $message_url,
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
                    admin_url('options-general.php?page=custom-login')))
//                    esc_url( admin_url( 'options-general.php?page=custom-login#custom_login_general' ) )
            ) :
            sprintf('%s', $announcement[0]->message);
        $html .= '</p></div>';

        if ((!$user_meta && 1 !== $user_meta) || $is_cl_screen) {
            echo $html;
        }
    }

    /**
     * Remove the admin notification.
     *
     * @return void
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
