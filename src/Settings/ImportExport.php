<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\Api\Postbox;
use TheFrosty\WpUtilities\Api\WpRemote;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Utils\Viewable;

/**
 * Class ImportExport
 * @package TheFrosty\CustomLogin\Settings
 */
class ImportExport extends AbstractContainerProvider
{

    use Viewable, Postbox, WpRemote;

    public const ACTION_DOWNLOAD_EXPORT = Factory::PREFIX . 'download_export';
    public const NONCE = Factory::PREFIX . 'nonce';

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction(WpSettingsApi::HOOK_INIT, [$this, 'init'], 12, 3);
        $this->addAction(WpSettingsApi::ACTION_PREFIX . 'before_sanitize_options', [$this, 'maybeImportSettings']);
        $this->addAction('admin_action_' . self::ACTION_DOWNLOAD_EXPORT, [$this, 'downloadSettingsExport']);
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
        $settings = include __DIR__ . '/../../config/import-export.php';
        foreach ($settings['sections'] as $section) {
            $section_manager->addSection(
                new SettingSection([
                    SettingSection::SECTION_ID => $section[SettingSection::SECTION_ID],
                    SettingSection::SECTION_TITLE => esc_html($section[SettingSection::SECTION_TITLE]),
                ])
            );
        }

        foreach ($settings['fields'] as $section_id => $fields) {
            foreach ($fields as $field) {
                $field_manager->addField(
                    new SettingField(array_merge($field, [SettingField::SECTION_ID => $section_id]))
                );
            }
        }
    }

    /**
     * Sanitize callback for Settings API before input into database.
     * @ref http://stackoverflow.com/a/10797086/558561
     */
    protected function maybeImportSettings(array $options): void
    {
        if (
            empty($options[OptionKey::SETTINGS_IMPORT]) ||
            base64_encode(
                base64_decode($options[OptionKey::SETTINGS_IMPORT], true)
            ) !== $options[OptionKey::SETTINGS_IMPORT]
        ) {
            return;
        }

        $import = json_decode(base64_decode($options[OptionKey::SETTINGS_IMPORT]), true);
        if (is_array($import)) {
            foreach ($import as $setting_key => $settings) {
                if ($settings !== false) {
                    if (update_option($setting_key, $settings)) {
                        add_settings_error(
                            $setting_key,
                            esc_attr('settings_updated'),
                            esc_html__('Custom Login settings successfully imported', 'custom-login'),
                            'updated'
                        );
                    }
                }
            }
        }
        \delete_option('custom_login_import_export');
    }

    /**
     * Export the settings.
     * @ref http://stackoverflow.com/a/16440501/558561
     */
    protected function downloadSettingsExport(): void
    {
        if (
            (!isset($_GET['action']) || $_GET['action'] !== self::ACTION_DOWNLOAD_EXPORT) ||
            (!isset($_GET[self::NONCE]) || !wp_verify_nonce($_GET[self::NONCE], 'export'))
        ) {
            wp_safe_redirect(remove_query_arg(['action', self::NONCE]));
            exit;
        }

        ignore_user_abort(true);
        nocache_headers();
        header('Content-type: text/plain; charset=utf-8');
        header(sprintf('Content-Disposition: attachment; filename=custom-login-settings-%s.txt', date('Y-n-j')));
        header('Expires: 0');

        echo $this->getEncodedSettings();
        exit;
    }

    /**
     * Return the full array of settings.
     * @return string
     */
    private function getEncodedSettings(): string
    {
        $settings = CustomLogin::getSettings();
        foreach ($settings['sections'] as $section) {
            $settings[$section[SettingSection::SECTION_ID]] = Options::getOptions($section[SettingSection::SECTION_ID]);
        }

        return base64_encode(json_encode($settings));
    }
}
