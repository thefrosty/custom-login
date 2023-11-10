<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Extensions;

use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class AddOn
 * @package TheFrosty\CustomLogin\Extensions
 */
abstract class AddOn implements WpHooksInterface
{

    use HooksTrait;

    /**
     * Settings fields.
     * @var array $fields
     */
    protected array $fields;

    /**
     * AddOn constructor.
     * @param Plugin $parent
     * @param string $file
     * @param string $version
     * @param string $domain
     * @param string $plugin_id
     * @param string $plugin_name
     */
    public function __construct(
        protected Plugin $parent,
        protected string $file,
        protected string $version,
        protected string $domain,
        protected string $plugin_id,
        protected string $plugin_name,
    ) {
        $this->domain = $this->parent->getSlug();
        $this->plugin_id = Factory::getSection($plugin_id);
    }

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('init', function (): void {
            $this->addAction(WpSettingsApi::HOOK_INIT, [$this, 'init'], 14, 3);
            if (\is_blog_admin()) {
                $this->addFilter('plugin_action_links', [$this, 'pluginActionLinks'], 10, 2);
            }
        });
    }

    /**
     * Initiate the addon setting to the Section & Field Manager classes.
     * @param SectionManager $section_manager
     * @param FieldManager $field_manager
     * @param WpSettingsApi $wp_settings_api
     */
    abstract protected function init(
        SectionManager $section_manager,
        FieldManager $field_manager,
        WpSettingsApi $wp_settings_api
    ): void;

    /**
     * Return the extensions settings fields.
     * @return array
     */
    abstract protected function getSettingsFields(): array;

    /**
     * Plugin Actions
     * @param string[] $actions An array of plugin action links.
     * @param string $plugin_file Path to the plugin file relative to the plugins' directory.
     */
    protected function pluginActionLinks(array $actions, string $plugin_file): array
    {
        if (\plugin_basename($this->file) === $plugin_file) {
            $settings_link = \sprintf(
                '<a href="%s">%s</a>',
                \admin_url(\sprintf('options-general.php?page=%s#%s', $this->parent->getSlug(), $this->plugin_id)),
                \__('Settings', 'custom-login')
            );
            \array_unshift($actions, $settings_link); // Before other links.
        }

        return $actions;
    }
}