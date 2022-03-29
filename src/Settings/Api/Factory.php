<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings\Api;

use Dwnload\WpSettingsApi\Api\PluginSettings;
use Dwnload\WpSettingsApi\SettingsApiFactory;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\WpUtilities\Plugin\Plugin;

/**
 * Class WpSettingsApiFactory
 * @package TheFrosty\CustomLogin\Settings
 */
class Factory
{

    public const PREFIX = 'custom_login_';
    public const SECTION_DESIGN = 'design';
    public const SECTION_GENERAL = 'general';
    public const SECTION_IMPORT_EXPORT = 'import_export';

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
            'version' => CustomLogin::VERSION,
        ]);
    }

    /**
     * Build the section w/ prefix.
     * @param string $section
     * @return string
     */
    public static function getSection(string $section): string
    {
        return self::PREFIX . $section;
    }
}
