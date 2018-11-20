<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\PluginAwareInterface;
use TheFrosty\WpUtilities\Plugin\PluginAwareTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class MailSettings
 * @package TheFrosty\CustomLogin\Settings
 */
class MailSettings implements WpHooksInterface, PluginAwareInterface
{
    use HooksTrait, PluginAwareTrait;

    public const FIELD_PRE_HEADER = 'pre_header';
    public const FIELD_HEADER_IMAGE = 'header_image';
    public const FIELD_HERO_IMAGE = 'hero_image';
    public const FIELD_FOOTER = 'footer';
    public const SECTION = CustomLogin::META_PREFIX . 'wp_mail_settings';

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction(App::ACTION_PREFIX . 'init', [$this, 'init'], 10, 2);
    }

    /**
     * Initiate our setting to the Section & Field Manager classes.
     * @param SectionManager $section_manager
     * @param FieldManager $field_manager
     */
    protected function init(SectionManager $section_manager, FieldManager $field_manager)
    {
        $section_id = $section_manager->addSection(
            new SettingSection([
                SettingSection::SECTION_ID => self::SECTION, // Unique section ID
                SettingSection::SECTION_TITLE => \esc_html__('Email Settings', 'custom-login'),
            ])
        );

        $field_manager->addField(
            new SettingField([
                SettingField::NAME => self::FIELD_PRE_HEADER,
                SettingField::LABEL => \esc_html__('Pre-header Text', 'custom-login'),
                SettingField::DESC => \esc_html_x(
                    '(Optional) This text will appear in the inbox preview, but not the email body. It can be used 
                    to supplement the email subject line or even summarize the email\'s contents. Extended text 
                    preheaders (~490 characters) seems like a better UX for anyone using a screenreader or voice-command 
                    apps like Siri to dictate the contents of an email. If this text is not included, email clients will 
                    automatically populate it using the text (including image alt text) at the start of the 
                    email\'s body.',
                    'Explanation on what an email pre-header text is.',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::DEFAULT => '',
                SettingField::SECTION_ID => $section_id,
            ])
        );

        $field_manager->addField(
            new SettingField([
                SettingField::NAME => self::FIELD_HEADER_IMAGE,
                SettingField::LABEL => \esc_html__('Header Image (logo)', 'custom-login'),
                SettingField::DESC => \esc_html__('200x50 (up to 600 wide) Logo Image', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_FILE,
                SettingField::SECTION_ID => $section_id,
            ])
        );

        $field_manager->addField(
            new SettingField([
                SettingField::NAME => self::FIELD_HERO_IMAGE,
                SettingField::LABEL => \esc_html__('Hero Image', 'custom-login'),
                SettingField::DESC => \esc_html__('1200x600 Hero Image', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_FILE,
                SettingField::SECTION_ID => $section_id,
            ])
        );

        $field_manager->addField(
            new SettingField([
                SettingField::NAME => self::FIELD_FOOTER,
                SettingField::LABEL => \esc_html__('Email Footer', 'custom-login'),
                SettingField::DESC => \esc_html__('Text displayed at the footer of the email.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::SANITIZE => function ($input): string {
                    return wp_kses($input, [
                        'a' => [
                            'href' => [],
                            'title' => [],
                        ],
                        'br' => [],
                        'em' => [],
                        'span' => [
                            'class' => [],
                        ],
                        'strong' => [],
                        'webversion' => [
                            'style' => [],
                        ],
                        'unsubscribe' => [
                            'style' => [],
                        ],
                    ]);
                },
                SettingField::DEFAULT => $this->getFooterDefault(),
                SettingField::SECTION_ID => $section_id,
            ])
        );
    }

    private function getFooterDefault(): string
    {
        ob_start();
        include $this->getPlugin()->getDirectory() . 'templates/settings/footer-default-value.php';
        return ob_get_clean();
    }
}
