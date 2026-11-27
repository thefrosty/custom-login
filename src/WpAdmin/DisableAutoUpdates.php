<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use function esc_html__;
use function is_object;
use function sprintf;

/**
 * Class DisableAutoUpdates
 * Turns off WordPress' plugin auto-updates for Custom Login when it's installed with Composer, so a WordPress.org
 * (PHP 7.4) update, e.g. one cached before the site switched to Composer, is never installed over the Composer copy.
 * Registered by custom-login.php alongside wp-utilities' DisablePluginUpdateCheck.
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class DisableAutoUpdates extends AbstractHookProvider
{

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addFilter('auto_update_plugin', [$this, 'disableAutoUpdate'], 999, 2);
        $this->addFilter('plugin_auto_update_setting_html', [$this, 'autoUpdateSettingHtml'], 10, 2);
    }

    /**
     * Never auto-update Custom Login (the decision `WP_Automatic_Updater::should_update()` asks for).
     * @param mixed $update Whether to update (bool), or null to use the user's auto-updates setting.
     * @param mixed $item The update offer; `$item->plugin` is the plugin basename.
     * @return mixed Other plugins' values are passed through unchanged.
     */
    protected function disableAutoUpdate(mixed $update, mixed $item): mixed
    {
        if (is_object($item) && ($item->plugin ?? null) === $this->getPlugin()->getBasename()) {
            return false;
        }

        return $update;
    }

    /**
     * Replace the "Enable auto-updates" toggle on the Plugins screen.
     * @param string $html The auto-updates column HTML.
     * @param string $plugin_file Path to the plugin file relative to the plugins directory.
     * @return string
     */
    protected function autoUpdateSettingHtml(string $html, string $plugin_file): string
    {
        if ($plugin_file !== $this->getPlugin()->getBasename()) {
            return $html;
        }

        return sprintf(
            '<span class="custom-login-auto-updates-composer">%s</span>',
            esc_html__('Updates are managed with Composer.', 'custom-login')
        );
    }
}
