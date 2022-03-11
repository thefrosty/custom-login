<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings\Api;

use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\SettingsApiFactory;
use TheFrosty\WpUtilities\Plugin\Plugin;

/**
 * Class WpSettingsApiFactory
 * @package TheFrosty\CustomLogin\Settings
 */
class Factory
{

    public const PREFIX = 'custom_login_';

    /**
     * Helper to get the App object.
     * @param Plugin $plugin The plugin slug
     * @return PluginSettings
     */
    public static function getPluginSettings(Plugin $plugin): PluginSettings
    {
        return SettingsApiFactory::create([
            'domain' => $plugin->getSlug(),
            'file' => __FILE__, // Path to WpSettingsApi file.
            'menu-slug' => $plugin->getSlug(),
            'menu-title' => \esc_html__('Custom Login', 'custom-login'),
            'page-title' => \esc_html__('Custom Login', 'custom-login'),
            'prefix' => self::PREFIX,
            'version' => '4.0.0-20220311',
        ]);
    }
}
