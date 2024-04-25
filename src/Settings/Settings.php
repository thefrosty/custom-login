<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\ActionHookName;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Postbox;
use TheFrosty\WpUtilities\Api\WpRemote;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Utils\Viewable;
use function __;
use function _x;
use function array_merge;
use function esc_html;
use function sprintf;
use function wp_add_inline_script;
use function wp_enqueue_code_editor;
use function wp_localize_script;

/**
 * Class Settings
 * @package TheFrosty\CustomLogin\Settings
 */
class Settings extends AbstractContainerProvider implements OptionKey
{

    use Postbox, Viewable, WpRemote;

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction(WpSettingsApi::HOOK_INIT, [$this, 'init'], 10, 3);
        $this->addFilter(ActionHookName::ADMIN_SETTINGS_ADMIN_SCRIPTS, [$this, 'adminScripts']);
        $this->addFilter(ActionHookName::ADMIN_SETTINGS_ADMIN_STYLES, [$this, 'adminStyles']);
        $this->addAction(ActionHookName::SETTINGS_SETTINGS_SIDEBARS, [$this, 'sidebarAboutTheAuthor'], 30);
        $this->addAction(ActionHookName::SETTINGS_SETTINGS_SIDEBARS, [$this, 'sidebarExtensions'], 35);
    }

    /**
     * Initiate our setting to the Section & Field Manager classes.
     * @param SectionManager $section_manager
     * @param FieldManager $field_manager
     * @param WpSettingsApi $wp_settings_api
     */
    protected function init(
        SectionManager $section_manager,
        FieldManager $field_manager,
        WpSettingsApi $wp_settings_api
    ): void {
        if (!$wp_settings_api->isCurrentMenuSlug($this->getPlugin()->getSlug())) {
            return;
        }

        $settings = CustomLogin::getSettings();
        foreach ($settings['sections'] as $section) {
            $section_manager->addSection(
                new SettingSection([
                    SettingSection::SECTION_ID => $section[SettingSection::SECTION_ID],
                    SettingSection::SECTION_TITLE => esc_html($section[SettingSection::SECTION_TITLE]),
                ])
            );
        }

        foreach ($settings['fields'] as $section_id => $fields) {
            foreach ($fields as $key => $field) {
                if ($field['name'] === OptionKey::BREAK_S) {
                    $field['name'] = sprintf($field['name'], $key);
                }
                $field_manager->addField(
                    new SettingField(array_merge($field, [SettingField::SECTION_ID => $section_id]))
                );
            }
        }
    }

    /**
     * Enqueue the code editor scripts for our settings.
     * @param array $scripts
     * @return array
     */
    protected function adminScripts(array $scripts): array
    {
        $types = ['css', 'html', 'javascript'];
        foreach ($types as $type) {
            $settings = ['codeEditor' => wp_enqueue_code_editor(['type' => "text/$type"])];
            $objectName = "LoginCodeEditor_$type";
            wp_localize_script('code-editor', $objectName, $settings);
            wp_add_inline_script(
                'code-editor',
                'jQuery(document).ready(function($) {
                const $textarea = $(\'textarea[data-codemirror="' . $type . '"]\')
                if ($textarea.length > 0) {
                    wp.codeEditor.initialize($textarea, ' . $objectName . ')
                }
                })'
            );
        }

        return $scripts;
    }

    /**
     * @param Style[] $styles
     * @return array
     */
    protected function adminStyles(array $styles): array
    {
        $styles[] = new Style([
            Style::HANDLE => 'custom-login',
            Style::SRC => $this->getPlugin()->getUrl('resources/css/settings.css'),
            Style::DEPENDENCIES => [],
            Style::VERSION => CustomLogin::VERSION,
            Style::MEDIA => 'screen',
        ]);

        return $styles;
    }

    /**
     * Build the about the author sidebar.
     * @param WpSettingsApi $wp_settings_api
     */
    protected function sidebarAboutTheAuthor(WpSettingsApi $wp_settings_api): void
    {
        if (!$wp_settings_api->isCurrentMenuSlug($this->getPlugin()->getSlug())) {
            return;
        }
        $content = $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->retrieve('sidebars/about-the-author.php');

        $this->postbox(
            'frosty-media-author',
            __('Custom Login', 'custom-login'),
            sprintf(
                $content,
                _x('Rate', 'rate; as in rate this plugin', 'custom-login'),
                _x('Author', 'the author of this plugin', 'custom-login'),
                __('Twitter', 'custom-login'),
                'https://github.com/thefrosty/custom-login/issues',
                'https://twitter.com/TheFrosty',
                'https://austin.passy.co'
            )
        );
    }

    /**
     * Build the extensions' sidebar.
     * @param WpSettingsApi $wp_settings_api
     */
    protected function sidebarExtensions(WpSettingsApi $wp_settings_api): void
    {
        if (!$wp_settings_api->isCurrentMenuSlug($this->getPlugin()->getSlug())) {
            return;
        }
        $content = $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->retrieve(
            'dashboard-widget/rest.php',
            [
                'posts' => $this->retrieveBodyCached(
                    'https://frosty.media/wp-json/wp/v2/extensions?per_page=6&plugin_tag=29',
                    WEEK_IN_SECONDS
                ),
                'renderContent' => false,
                'widgetId' => 'custom-login-extensions',
            ]
        );

        $this->postbox(
            'custom-login-extensions',
            sprintf(__('Extensions %s', 'custom-login'), '<small class="dashicons dashicons-external"></small>'),
            $content
        );
    }
}
