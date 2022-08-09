<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Utils\Viewable;
use function add_options_page;
use function admin_url;
use function get_option;
use function printf;
use function sprintf;
use function update_option;
use function version_compare;
use function wp_send_json_success;

/**
 * Class SettingsUpgrades
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class SettingsUpgrades extends AbstractContainerProvider
{

    use Viewable;

    public const ACTION_NONCE = self::class;
    public const AJAX_ACTION = 'custom_login_trigger_upgrades';
    public const OPTION_VERSION = 'custom_login_version';

    /**
     * Options page menu slug.
     * @var string $menu_slug
     */
    private string $menu_slug;

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->menu_slug = sprintf('%s/upgrades', $this->getPlugin()->getSlug());
        $this->addAction('load-index.php', function (): void {
            $this->addAction('admin_notices', [$this, 'adminNotice']);
        });
        $this->addAction('admin_menu', [$this, 'adminMenu']);
        $this->addAction('wp_ajax_' . self::AJAX_ACTION, [$this, 'triggerUpgrades']);
    }

    /**
     * Display Upgrade Notices.
     * @since 2.0
     */
    protected function adminNotice(): void
    {
        $database_version = get_option(self::OPTION_VERSION);

        if (!$database_version) {
            // 3.2.15 is the last version before 4.0.0
            $database_version = '4.0.1';
        }

        // Version less than 4.0.1
        if (version_compare($database_version, '4.0', '<')) {
            $this->renderUpgradeNotice(
                sprintf(
                    esc_html__('Custom Login: Version %s has some settings changes.', 'custom-login'),
                    CustomLogin::VERSION
                ),
                esc_html_x(
                    'Please click here the run the updates',
                    'Button text to trigger settings updates',
                    'custom-login'
                ),
            );
        }
    }

    /**
     * Add Submenu Upgrade page.
     * @since 1.0
     */
    protected function adminMenu(): void
    {
        add_options_page(
            esc_html__('Custom Login Upgrades', 'custom-login'),
            esc_html__('Custom Login Upgrades', 'custom-login'),
            'update_plugins',
            $this->menu_slug,
            function (): void {
                $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->render('settings/upgrades');
            }
        );
        remove_submenu_page('options-general.php', $this->menu_slug);
    }

    /**
     * Triggers all upgrade functions.
     * @since 2.0
     */
    protected function triggerUpgrades(): void
    {
        check_ajax_referer(SettingsUpgrades::ACTION_NONCE);

        $version = get_option(self::OPTION_VERSION);

        if (!$version) {
            // 3.2.15 is the last version before 4.0.0
            $version = '3.2.15';
            add_option(self::OPTION_VERSION, $version);
        }

        // Version less than 4.0.1
        if (!$version || version_compare($version, '4.0.1', '<')) {
            $this->v401Upgrades();
        }

        update_option(self::OPTION_VERSION, CustomLogin::VERSION);
        wp_send_json_success();
    }

    /**
     * Render the admin notice for settings upgrades.
     * @param string $message_text
     * @param string $button_text
     */
    private function renderUpgradeNotice(string $message_text, string $button_text): void
    {
        printf(
            $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->retrieve(
                'notices/settings-upgrades.php',
                [
                    'url' => admin_url(sprintf('options-general.php?page=%s', $this->menu_slug)),
                ]
            ),
            $message_text,
            $button_text,
        );
    }

    /**
     * Upgrade Routine for < v4.0.0
     * @since 4.0.1
     */
    private function v401Upgrades(): void
    {
    }
}
