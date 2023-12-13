<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\Api\Activator;
use TheFrosty\CustomLogin\Api\Cron;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\WpUtilities\Api\WpRemote;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Utils\Viewable;
use function array_keys;
use function array_merge;
use function current_time;
use function function_exists;
use function get_bloginfo;
use function get_option;
use function get_plugins;
use function home_url;
use function sprintf;
use function TheFrosty\CustomLogin\isSettingsPage;
use function wp_get_theme;
use function wp_next_scheduled;
use function wp_schedule_single_event;

/**
 * Class Tracking
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class Tracking extends AbstractContainerProvider
{

    use Activator, Viewable, WpRemote;

    public const OPTION_HIDE_TRACKING_NOTICE = Factory::PREFIX . 'hide_tracking_notice';
    public const OPTION_TRACKING_LAST_SEND = Factory::PREFIX . 'tracking_last_send';
    private const ACTION = 'cl_checkin';
    private const HOOK_SEND_CHECKIN = Factory::PREFIX . 'send_check_in';
    private const OPT_INTO_TRACKING = 'cl_opt_into_tracking';
    private const OPT_OUT_OF_TRACKING = 'cl_opt_out_of_tracking';

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction(self::HOOK_SEND_CHECKIN, [$this, 'sendCheckIn'], 10, 2);
        $this->addAction(Cron::HOOK_WEEKLY, [$this, 'sendCheckIn'], 10, 2);
        $this->addAction(WpSettingsApi::ACTION_PREFIX . 'after_sanitize_options', [$this, 'afterSanitizeOptions']);
        $this->addAction('admin_action_' . self::OPT_INTO_TRACKING, [$this, 'checkForOptIn']);
        $this->addAction('admin_action_' . self::OPT_OUT_OF_TRACKING, [$this, 'checkForOptOut']);
        $this->addAction('admin_notices', [$this, 'adminNotice']);
    }

    /**
     * Runs on plugin install.
     * @since 3.0.0
     * @update 4.0.0 Change to scheduled (cron) event.
     */
    public function activate(): void
    {
        $this->scheduleCheckIn(['on_activation' => 'yes'], true);
    }

    /**
     * Send a check-in request.
     */
    protected function sendCheckIn(array $extra_data = [], bool $force = false): void
    {
        if (!$this->isTrackingAllowed() && !$force) {
            return;
        }

        // Send a maximum of once per week
        $last_send = $this->getLastSend();
        if ($last_send && $last_send > strtotime('-1 week')) {
            return;
        }

        $response = $this->wpRemotePost(
            add_query_arg('edd_action', self::ACTION, CustomLogin::getApiUrl('cl-checkin-api/')),
            [
                'body' => $this->getBody($extra_data),
                'user-agent' => 'CustomLogin/' . CustomLogin::VERSION . '; ' . get_bloginfo('url'),
            ]
        );

        if (!is_wp_error($response)) {
            update_option(self::OPTION_TRACKING_LAST_SEND, time());
        }
    }

    /**
     * Schedule a single check-in request.
     */
    protected function scheduleCheckIn(array $extra_data = [], bool $force = false): void
    {
        if (!wp_next_scheduled(self::HOOK_SEND_CHECKIN, [$extra_data, $force])) {
            wp_schedule_single_event(current_time('timestamp'), self::HOOK_SEND_CHECKIN, [$extra_data, $force]);
        }
    }

    /**
     * Check for a new opt-in on settings save.
     * This runs during the sanitation of General settings, thus the return
     * @param array $input
     * @return array
     */
    protected function afterSanitizeOptions(array $input): array
    {
        // Send an initial check in on settings save.
        if (isset($input[OptionKey::TRACKING]) && $input[OptionKey::TRACKING] === OptionValue::ON) {
            $this->scheduleCheckIn(['on_activation' => 'settings', 'mailchimp_sub' => 'yes'], true);
        }

        return $input;
    }

    /**
     * Check for a new opt-in via the admin notice.
     */
    protected function checkForOptIn(): void
    {
        if (!isset($_GET['_wpnonce']) || !\wp_verify_nonce($_GET['_wpnonce'], self::ACTION)) {
            return;
        }
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $options[OptionKey::TRACKING] = OptionValue::ON;
        update_option($section_id, $options);
        update_option(self::OPTION_HIDE_TRACKING_NOTICE, '1');
        $this->scheduleCheckIn(['on_activation' => 'admin notice', 'mailchimp_sub' => 'yes'], true);

        wp_safe_redirect(esc_url_raw(remove_query_arg('action', wp_get_referer())));
        exit;
    }

    /**
     * Check for a new opt-in via the admin notice.
     */
    protected function checkForOptOut(): void
    {
        if (!isset($_GET['_wpnonce']) || !\wp_verify_nonce($_GET['_wpnonce'], self::ACTION)) {
            return;
        }
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $options[OptionKey::TRACKING] = OptionValue::OFF;
        update_option($section_id, $options);
        update_option(self::OPTION_HIDE_TRACKING_NOTICE, '1');

        wp_safe_redirect(esc_url_raw(remove_query_arg('action', wp_get_referer())));
        exit;
    }

    /**
     * Display the admin notice to users that have not opted-in or out.
     * Don't show this notice when _not_ on the Custom Login settings page.
     * Don't show this notice when the hide tracking notice has been "saved".
     */
    protected function adminNotice(): void
    {
        if (!isSettingsPage($this->getPlugin())) {
            return;
        }

        // Check the notice setting _after_ settings page check..
        if (!empty(get_option(self::OPTION_HIDE_TRACKING_NOTICE))) {
            return;
        }

        printf(
            $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->retrieve(
                'notices/admin-notice.php',
                [
                    'opt_in_url' => wp_nonce_url(
                        add_query_arg('action', self::OPT_INTO_TRACKING, admin_url('admin.php')),
                        self::ACTION
                    ),
                    'opt_out_url' => wp_nonce_url(
                        add_query_arg('action', self::OPT_OUT_OF_TRACKING, admin_url('admin.php')),
                        self::ACTION
                    ),
                ]
            ),
            esc_html__(
                'Allow Custom Login to anonymously track how this plugin is used and help us make the plugin better?',
                'custom-login'
            ),
            esc_html__('Allow', 'custom-login'),
            esc_html__('Do not allow', 'custom-login')
        );
    }

    /**
     * Check if the user has opted into tracking
     * @return bool
     */
    private function isTrackingAllowed(): bool
    {
        return Options::getOption(
                OptionKey::TRACKING,
                Factory::getSection(Factory::SECTION_GENERAL),
                OptionValue::OFF
            ) === OptionValue::ON;
    }

    /**
     * Set up the data that is going to be tracked.
     * @param array $extra_data
     * @return array
     */
    private function getBody(array $extra_data = []): array
    {
        $data = [];

        $theme_data = wp_get_theme();
        $theme = sprintf('%s (%s)', $theme_data->Name ?? 'Unknown', $theme_data->Version ?? 'Unknown');

        $data['url'] = home_url();
        $data['version'] = get_bloginfo('version');
        $data['theme'] = $theme;
        $data['email'] = get_bloginfo('admin_email');

        // Retrieve current plugin information
        if (!function_exists('get_plugins')) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugins = array_keys(get_plugins());
        $active_plugins = get_option('active_plugins', []);

        foreach ($plugins as $key => $plugin) {
            if (in_array($plugin, $active_plugins)) {
                unset($plugins[$key]); // Remove active plugins from list, so we can show active and inactive separately
            }
        }

        $data['active_plugins'] = $active_plugins;
        $data['inactive_plugins'] = $plugins;
        $data['post_count'] = wp_count_posts()->publish;
        $data['cl_version'] = CustomLogin::VERSION;
        $data = array_merge($data, $extra_data);
        sort($data);

        return $data;
    }

    /**
     * Get the last time a checkin was sent.
     * @return int
     */
    private function getLastSend(): int
    {
        return absint(get_option(self::OPTION_TRACKING_LAST_SEND, time()));
    }
}
