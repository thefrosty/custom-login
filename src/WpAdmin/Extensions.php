<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use Dwnload\WpSettingsApi\ActionHookName;
use TheFrosty\CustomLogin\AbstractContainerProvider;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Postbox;
use TheFrosty\WpUtilities\Utils\Viewable;
use function is_string;
use function sprintf;

/**
 * Class Extensions
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class Extensions extends AbstractContainerProvider
{

    use Postbox, Viewable;

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('admin_menu', [$this, 'adminMenu']);
        $this->addAction(ActionHookName::SETTINGS_SETTINGS_SIDEBARS, [$this, 'sidebarExtensions'], 22);
    }

    /**
     * Register our "hidden" Custom Login extensions options page.
     */
    protected function adminMenu(): void
    {
        $options_page = add_options_page(
            esc_html__('Custom Login Extensions', 'custom-login'),
            esc_html__('Custom Login Extensions', 'custom-login'),
            'install_plugins',
            sprintf('%s/extensions', $this->getPlugin()->getSlug()),
            function (): void {
                $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->render(
                    'settings/extensions',
                    [
                        'checkout_url' => CustomLogin::getApiUrl('checkout/'),
                        'extensions' => include __DIR__ . '/../../config/extensions.php',
                    ]
                );
            }
        );

        remove_submenu_page('options-general.php', sprintf('%s/extensions', $this->getPlugin()->getSlug()));
        if (is_string($options_page)) {
            $this->addAction('load-' . $options_page, function (): void {
                $this->addAction('admin_enqueue_scripts', function (): void {
                    wp_enqueue_style(
                        $this->getPlugin()->getSlug(),
                        $this->getPlugin()->getUrl('resources/css/extensions.css'),
                        false,
                        CustomLogin::VERSION,
                        'screen'
                    );
                });
            });
        }
    }
    /**
     * Build the extensions' sidebar.
     */
    protected function sidebarExtensions(): void
    {
        $this->postbox(
            'custom-login-extensions-installer',
            __('Extensions Installer', 'custom-login'),
            sprintf(
                __(
                    'Install Custom Login extensions on <a href="%s">this page</a> with a valid license key. <small>Purchase your license key by clicking the appropriate link below</small>.',
                    'custom-login'
                ),
                sprintf(admin_url('options-general.php?page=%s/extensions'), $this->getPlugin()->getSlug())
            )
        );
    }
}
