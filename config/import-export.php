<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\ImportExport;
use TheFrosty\CustomLogin\Settings\OptionKey;

return [
    'sections' => [
        [
            SettingSection::SECTION_ID => Factory::getSection(Factory::SECTION_IMPORT_EXPORT),
            SettingSection::SECTION_TITLE => __('Import/Export', 'custom-login'),
        ],
    ],
    'fields' => [
        Factory::getSection(Factory::SECTION_IMPORT_EXPORT) => [
            [
                SettingField::NAME => OptionKey::SETTINGS_IMPORT,
                SettingField::LABEL => esc_html__(OptionKey::SETTINGS_IMPORT, 'custom-login'),
                SettingField::DESC => '',
                SettingField::DEFAULT => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::SANITIZE => '__return_empty_string',
            ],
            [
                SettingField::NAME => OptionKey::SETTINGS_EXPORT,
                SettingField::LABEL => esc_html__('Export', 'custom-login'),
                SettingField::DESC => sprintf(
                    __(
                        'This textarea is always pre-filled with the current settings. Copy these settings for import at a later time, or <a href="%s">download</a> them.',
                        'custom-login'
                    ),
                    esc_url(
                        wp_nonce_url(
                            add_query_arg(['action' => ImportExport::ACTION_DOWNLOAD_EXPORT], admin_url('admin.php')),
                            'export',
                            ImportExport::NONCE
                        )
                    )
                ),
                SettingField::DEFAULT => $this->getEncodedSettings(),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::ATTRIBUTES => [
                    'readonly' => 'readonly',
                ],
                SettingField::SANITIZE => '__return_empty_string',
            ],
        ],
    ],
];
