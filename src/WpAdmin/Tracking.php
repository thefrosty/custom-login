<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\Api\Activator;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\WpUtilities\Api\WpRemote;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Utils\Viewable;
use function add_action;
use function array_keys;
use function array_merge;
use function function_exists;
use function get_bloginfo;
use function get_option;
use function get_plugins;
use function home_url;
use function sprintf;
use function wp_get_theme;

/**
 * Class Tracking
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class Tracking extends AbstractContainerProvider
{

    use Activator, Viewable, WpRemote;

    public const API = CustomLogin::API_URL . 'cl-checkin-api/?edd_action=cl_checkin';
    public const OPTION_HIDE_TRACKING_NOTICE = Factory::PREFIX . 'hide_tracking_notice';
    public const OPTION_TRACKING_LAST_SEND = Factory::PREFIX . 'tracking_last_send';
    private const OPT_INTO_TRACKING = 'cl_opt_into_tracking';
    private const OPT_OUT_OF_TRACKING = 'cl_opt_out_of_tracking';

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('custom_login_weekly_scheduled_events', [$this, 'sendCheckIn']);
        $this->addAction(WpSettingsApi::ACTION_PREFIX . 'after_sanitize_options', [$this, 'afterSanitizeOptions']);
        $this->addAction('admin_action_' . self::OPT_INTO_TRACKING, [$this, 'checkForOptIn']);
        $this->addAction('admin_action_cl_opt_out_of_tracking', [$this, 'checkForOptOut']);
        $this->addAction('admin_notices', [$this, 'adminNotice']);
    }

    /**
     * Runs on plugin install.
     * @since 3.0.0
     */
    public function activate(): void
    {
        $this->sendCheckIn(['on_activation' => 'yes'], true);
    }

    /**
     * Send a check-in request.
     */
    protected function sendCheckIn(array $extra_data = [], bool $force = false)
    {
        if (!$this->isTrackingAllowed() && !$force) {
            return;
        }

        // Send a maximum of once per week
        $last_send = $this->getLastSend();
        if ($last_send && $last_send > strtotime('-1 week')) {
            return;
        }

        add_action('shutdown', function () use ($extra_data): void {
            $response = $this->wpRemotePost(
                self::API,
                [
                    'body' => $this->getBody($extra_data),
                    'user-agent' => 'CustomLogin/' . CustomLogin::VERSION . '; ' . get_bloginfo('url'),
                ]
            );

            if (!is_wp_error($response)) {
                update_option(self::OPTION_TRACKING_LAST_SEND, time());
            }
        });
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
            $this->sendCheckIn(['on_activation' => 'settings', 'mailchimp_sub' => 'yes'], true);
        }

        return $input;
    }

    /**
     * Check for a new opt-in via the admin notice.
     */
    protected function checkForOptIn(): void
    {
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $options[OptionKey::TRACKING] = OptionValue::ON;
        update_option($section_id, $options);
        update_option(self::OPTION_HIDE_TRACKING_NOTICE, '1');
        $this->sendCheckIn(['on_activation' => 'admin notice', 'mailchimp_sub' => 'yes'], true);

        wp_redirect(esc_url(remove_query_arg('action')));
        exit;
    }

    /**
     * Check for a new opt-in via the admin notice.
     */
    protected function checkForOptOut(): void
    {
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $options[OptionKey::TRACKING] = OptionValue::OFF;
        update_option($section_id, $options);
        update_option(self::OPTION_HIDE_TRACKING_NOTICE, '1');

        wp_redirect(esc_url(remove_query_arg('action')));
        exit;
    }

    /**
     * Display the admin notice to users that have not opted-in or out
     */
    protected function adminNotice(): void
    {
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $hide_notice = get_option(self::OPTION_HIDE_TRACKING_NOTICE);

        if (
            $hide_notice === true ||
            (!empty($options[OptionKey::ADMIN_NOTICES]) && $options[OptionKey::ADMIN_NOTICES] === OptionValue::OFF) ||
            (!empty($options[OptionKey::TRACKING]) && $options[OptionKey::TRACKING] === OptionValue::OFF) ||
            !current_user_can('manage_options')
        ) {
            return;
        }

        printf(
            $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->retrieve(
                'notices/admin-notice.php',
                [
                    'opt_in_url' => add_query_arg('action', self::OPT_INTO_TRACKING),
                    'opt_out_url' => add_query_arg('action', self::OPT_OUT_OF_TRACKING),
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
