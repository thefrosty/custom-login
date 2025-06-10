<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use Dwnload\WpSettingsApi\Api\Options;
use TheFrosty\CustomLogin\Api\Activator;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\CustomLogin\WpAdmin\Extensions;
use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;

use function add_query_arg;
use function admin_url;
use function apply_filters;
use function printf;
use function sprintf;
use function untrailingslashit;
use function update_option;

/**
 * Class CustomLogin
 * @package TheFrosty\CustomLogin
 */
class CustomLogin extends AbstractHookProvider
{

    use Activator;

    public const API_URL = 'https://frosty.media/';
    public const OPTION = 'custom_login';
    public const VERSION = '4.5.2';

    /**
     * Get the API URL.
     * @param string $uri
     * @return string
     */
    public static function getApiUrl(string $uri = ''): string
    {
        return sprintf('%1$s/%2$s', untrailingslashit(getenv('CUSTOM_LOGIN_API_URL') ?: self::API_URL), $uri);
    }

    /**
     * Get the default settings array.
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function getSettings(): array
    {
        return include __DIR__ . '/../config/settings.php';
    }

    /**
     * Runs on plugin install.
     * @since 3.1
     * @updated 4.0.0
     */
    public function activate(): void
    {
        $section_id = Factory::getSection(Factory::SECTION_GENERAL);
        $options = Options::getOptions($section_id);
        $options[OptionKey::ACTIVE] = OptionValue::ON;
        update_option($section_id, $options);
    }

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addFilter('plugin_action_links', [$this, 'pluginActionLinks'], 10, 2);
        $this->addFilter('plugin_row_meta', [$this, 'pluginRowMeta'], 10, 2);
        $this->addAction('init', [$this, 'i18n']);
        $this->addAction('login_head', [$this, 'metaGenerator'], 1);
        do_action(Factory::PREFIX . 'actions');
    }

    /**
     * Plugins row action links.
     * @param string[] $actions An array of plugin action links. By default, this can include
     *                              'activate', 'deactivate', and 'delete'. With Multisite active
     *                              this can also include 'network_active' and 'network_only' items.
     * @param string $plugin_file Path to the plugin file relative to the plugins directory.
     * @return array
     */
    protected function pluginActionLinks(array $actions, string $plugin_file): array
    {
        if ($this->getPlugin()->getBasename() === $plugin_file) {
            array_unshift(
                $actions,
                sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(sprintf(admin_url('options-general.php?page=%s'), $this->getPlugin()->getSlug())),
                    esc_html__('Settings', 'custom-login')
                )
            );

            if (!empty(Extensions::getExtensions())) {
                $actions[] = sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg('plugin_status', 'all', admin_url('plugins.php')),
                    esc_html__('Show Extensions', 'custom-login')
                );
            }
        }

        return $actions;
    }

    /**
     * Plugin row meta links.
     * @param string[] $plugin_meta An array of the plugin's metadata, including
     *                              the version, author, author URI, and plugin URI.
     * @param string $plugin_file Path to the plugin file relative to the plugins directory.
     * @return array
     */
    protected function pluginRowMeta(array $plugin_meta, string $plugin_file): array
    {
        if ($this->getPlugin()->getBasename() !== $plugin_file) {
            return $plugin_meta;
        }

        $links = [
            sprintf(
                '<a href="%s">%s</a>',
                esc_url(
                    sprintf(
                        admin_url('options-general.php?page=%s/extensions'),
                        $this->getPlugin()->getSlug()
                    )
                ),
                esc_html__('Extension Installer', 'custom-login')
            ),
            sprintf(
                '<a href="%s">%s</a>',
                esc_url('https://frosty.media/plugin/tag/custom-login-extension/'),
                esc_html__('Extensions', 'custom-login')
            ),
        ];

        return array_merge($plugin_meta, $links);
    }

    /**
     * Load plugin translations.
     */
    protected function i18n(): void
    {
        load_plugin_textdomain('custom-login', false, __DIR__ . '/../languages/');
    }

    /**
     * Adds the meta generator into the `<head>`.
     * @since 3.0.0
     * @updated 4.0.0 Add filter to remove generator.
     */
    protected function metaGenerator(): void
    {
        if (apply_filters(sprintf('%1$s_disable_meta_generator', self::OPTION), false) === true) {
            return;
        }
        printf('<meta name="generator" content="Custom Login %1$s">%2$s', self::VERSION, PHP_EOL);
    }
}
