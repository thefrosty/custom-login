<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Pro\Modules\Module;
use TheFrosty\CustomLogin\Settings\Api\CustomFieldTypes;
use const TheFrosty\CustomLogin\VERSION;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\PluginAwareInterface;
use TheFrosty\WpUtilities\Plugin\PluginAwareTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class ActiveModuleSettings
 * @package TheFrosty\CustomLogin\Settings
 */
class ActiveModuleSettings implements WpHooksInterface, PluginAwareInterface
{
    use HooksTrait, PluginAwareTrait;

    public const FIELD_ACTIVE_MODULES = 'active_modules';
    public const SECTION = CustomLogin::META_PREFIX . 'pro_settings';
    public const TAG_REGISTERED_MODULES = CustomLogin::HOOK_PREFIX . 'pro/registered_modules';

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction(App::ACTION_PREFIX . 'init', [$this, 'init'], 10, 2);
        $this->addAction(App::FILTER_PREFIX . 'settings_page_loaded', function () {
            $this->addAction('admin_enqueue_scripts', [$this, 'adminEnqueueScripts'], 101);
        });
    }

    /**
     * Initiate our setting to the Section & Field Manager classes.
     * @param SectionManager $section_manager
     * @param FieldManager $field_manager
     */
    protected function init(SectionManager $section_manager, FieldManager $field_manager)
    {
        $field_manager->addField(
            new SettingField([
                SettingField::NAME => self::FIELD_ACTIVE_MODULES,
                SettingField::LABEL => \esc_html__('Modules', 'custom-login'),
                SettingField::DESC => \esc_html__('Turn on/off select pro modules.', 'custom-login'),
                SettingField::TYPE => CustomFieldTypes::FIELD_TYPE_MODULES,
                SettingField::CLASS_OBJECT => new CustomFieldTypes(),
                SettingField::OPTIONS => $this->getModuleOptions(),
                'attributes' => $this->getModuleAttributes(),
                SettingField::SECTION_ID => $section_manager->addSection(
                    new SettingSection([
                        SettingSection::SECTION_ID => self::SECTION, // Unique section ID
                        SettingSection::SECTION_TITLE => \esc_html__('Custom Login Pro Modules', 'custom-login'),
                    ])
                ),
            ])
        );
    }

    /**
     * Add out custom styles.
     */
    protected function adminEnqueueScripts()
    {
        $styles = [
            new Style([
                Style::HANDLE => CustomLogin::HOOK_PREFIX . 'checkbox-toggle-slider',
                Style::SRC => $this->getPlugin()->getUrl('assets/css/checkbox-toggle-slider.css'),
                Style::DEPENDENCIES => [],
                Style::VERSION => VERSION,
                Style::MEDIA => 'screen',

            ]),
        ];
        /** @var Style $style */
        foreach ($styles as $style) {
            if (!\wp_style_is($style->getHandle(), 'registered')) {
                \wp_register_style(
                    $style->getHandle(),
                    $style->getSrc(),
                    $style->getDependencies(),
                    $style->getVersion(),
                    $style->getMedia()
                );
                \wp_enqueue_style($style->getHandle());
                continue;
            }
            \wp_enqueue_style($style->getHandle());
        }
    }

    /**
     * Returns the Module options key => value pair.
     * @return Module[]
     */
    private function getModuleOptions(): array
    {
        $options = [];
        $modules = $this->getRegisteredModules();
        \array_walk($modules, function (array $module_data) use (&$options) {
            $module = new Module($module_data);
            $options[$module->getFullyQualifiedClass()] = $module->getTitle();
        });

        return $options;
    }

    /**
     * Returns the Module options key => value pair.
     * @return Module[]
     */
    private function getModuleAttributes(): array
    {
        $attributes = [];
        $modules = $this->getRegisteredModules();
        \array_walk($modules, function (array $module_data) use (&$attributes) {
            $module = new Module($module_data);
            $attributes[$module->getFullyQualifiedClass()] = [
                Module::DESCRIPTION => $module->getDescription(),
                Module::IMAGE => $module->getImage(),
            ];
        });

        return $attributes;
    }

    /**
     * Return all registered modules.
     * @return array
     */
    private function getRegisteredModules(): array
    {
        static $modules;
        $modules = $modules ?: (array)\apply_filters(self::TAG_REGISTERED_MODULES, []);
        return $modules;
    }
}
