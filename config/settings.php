<?php declare(strict_types=1);

use Dwnload\WpSettingsApi\Api\SettingField;
use Dwnload\WpSettingsApi\Api\SettingSection;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;

return [
    'sections' => [
        [
            SettingSection::SECTION_ID => Factory::getSection(Factory::SECTION_DESIGN),
            SettingSection::SECTION_TITLE => __('Design Settings', 'custom-login'),
        ],
        [
            SettingSection::SECTION_ID => Factory::getSection(Factory::SECTION_GENERAL),
            SettingSection::SECTION_TITLE => __('General Settings', 'custom-login'),
        ],
    ],
    'fields' => [
        Factory::getSection(Factory::SECTION_DESIGN) => [
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf(
                    '<h4>%s</h4>',
                    __('<abbr title="Hyper Text Markup Language">HTML</abbr>', 'custom-login')
                ),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::HTML_BACKGROUND_COLOR,
                SettingField::LABEL => __('Background color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::HTML_BACKGROUND_URL,
                SettingField::LABEL => __('Background image', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_FILE,
                SettingField::DEFAULT => '',
                SettingField::SIZE => 'medium',
                SettingField::SANITIZE => '\sanitize_text_field',
            ],
            [
                SettingField::NAME => OptionKey::HTML_BACKGROUND_POSITION,
                SettingField::LABEL => __('Background position', 'custom-login'),
                SettingField::DESC => sprintf(
                    '<a href="https://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>.',
                    __('html background position', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'left top' => 'left top',
                    'left center' => 'left center',
                    'left bottom' => 'left bottom',
                    'right top' => 'right top',
                    'right center' => 'right center',
                    'right bottom' => 'right bottom',
                    'center top' => 'center top',
                    'center center' => 'center center',
                    'center bottom' => 'center bottom',
                ],
            ],
            [
                SettingField::NAME => OptionKey::HTML_BACKGROUND_REPEAT,
                SettingField::LABEL => __('Background repeat', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'no-repeat' => 'no-repeat',
                    'repeat' => 'repeat',
                    'repeat-x' => 'repeat-x',
                    'repeat-y' => 'repeat-y',
                ],
            ],
            [
                SettingField::NAME => OptionKey::HTML_BACKGROUND_SIZE,
                SettingField::LABEL => __('Background size', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'none' => 'none',
                    'cover' => 'cover',
                    'contain' => 'contain',
                    'flex' => 'flex',
                ],
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Logo', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::HIDE_WP_LOGO,
                SettingField::LABEL => __('Hide the WP logo', 'custom-login'),
                SettingField::DESC => __('This setting hides the h1 element.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_URL,
                SettingField::LABEL => __('Image', 'custom-login'),
                SettingField::DESC => __(
                    'I would suggest a max width of 320px, the default form width. You can widen the width (setting below).',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_FILE,
                SettingField::DEFAULT => '',
                SettingField::SIZE => 'medium',
                SettingField::SANITIZE => '\sanitize_text_field',
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_SIZE_WIDTH,
                SettingField::LABEL => __('Image width', 'custom-login'),
                SettingField::DESC => __(
                    'Enter your desired image height (All not integers will be removed).',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_NUMBER,
                SettingField::SIZE => 'small',
                SettingField::DEFAULT => '',
                SettingField::SANITIZE => 'int',
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_SIZE_HEIGHT,
                SettingField::LABEL => __('Image height', 'custom-login'),
                SettingField::DESC => __('Enter your desired image height (All not integers will be removed).',
                    'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_NUMBER,
                SettingField::SIZE => 'small',
                SettingField::DEFAULT => '',
                SettingField::SANITIZE => 'int',
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_POSITION,
                SettingField::LABEL => __('Background position', 'custom-login'),
                SettingField::DESC => sprintf(
                    '<a href="https://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>',
                    __('html background position', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'left top' => 'left top',
                    'left center' => 'left center',
                    'left bottom' => 'left bottom',
                    'right top' => 'right top',
                    'right center' => 'right center',
                    'right bottom' => 'right bottom',
                    'center top' => 'center top',
                    'center center' => 'center center',
                    'center bottom' => 'center bottom',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_REPEAT,
                SettingField::LABEL => __('Background repeat', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'no-repeat' => 'no-repeat',
                    'repeat' => 'repeat',
                    'repeat-x' => 'repeat-x',
                    'repeat-y' => 'repeat-y',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGO_BACKGROUND_SIZE,
                SettingField::LABEL => __('Background size', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'none' => 'none',
                    'cover' => 'cover',
                    'contain' => 'contain',
                    'flex' => 'flex',
                ],
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Login Form', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::LOGO_FORCE_FORM_MAX_WIDTH,
                SettingField::LABEL => __('Force max-width', 'custom-login'),
                SettingField::DESC => __(
                    'If checked and the login form width (set below) is not empty, a CSS rule of <code>width</code> will be applied on the logo wrapper element <code>.login h1</code>. These settings apply to the Logo image (when background size is used).',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_WIDTH,
                SettingField::LABEL => __('Width', 'custom-login'),
                SettingField::DESC => __('Change the default width of the login form.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_NUMBER,
                SettingField::SIZE => 'small',
                SettingField::DEFAULT => '320',
                SettingField::SANITIZE => 'int',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_WIDTH_UNIT,
                SettingField::LABEL => __('Width Unit', 'custom-login'),
                SettingField::DESC => sprintf(
                    '<a href="https://www.w3schools.com/cssref/css_units.asp" target="_blank">%s</a>',
                    __('login form width unit value.', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::DEFAULT => 'px',
                SettingField::OPTIONS => [
                    'px' => 'px',
                    'em' => 'em',
                    'rem' => 'rem',
                    'vw' => 'vw',
                    'vh' => 'vh',
                    '%' => '%',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BACKGROUND_COLOR,
                SettingField::LABEL => __('Background color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BACKGROUND_URL,
                SettingField::LABEL => __('Background URL', 'custom-login'),
                SettingField::DESC => __('Add a background image to the login form.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_FILE,
                SettingField::DEFAULT => '',
                SettingField::SIZE => 'medium',
                SettingField::SANITIZE => '\sanitize_text_field',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BACKGROUND_POSITION,
                SettingField::LABEL => __('Background position', 'custom-login'),
                SettingField::DESC => sprintf(
                    '<a href="https://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>',
                    __('html background position', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'left top' => 'left top',
                    'left center' => 'left center',
                    'left bottom' => 'left bottom',
                    'right top' => 'right top',
                    'right center' => 'right center',
                    'right bottom' => 'right bottom',
                    'center top' => 'center top',
                    'center center' => 'center center',
                    'center bottom' => 'center bottom',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BACKGROUND_REPEAT,
                SettingField::LABEL => __('Background repeat', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'no-repeat' => 'no-repeat',
                    'repeat' => 'repeat',
                    'repeat-x' => 'repeat-x',
                    'repeat-y' => 'repeat-y',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BACKGROUND_SIZE,
                SettingField::LABEL => __('Background size', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::OPTIONS => [
                    'none' => 'none',
                    'cover' => 'cover',
                    'contain' => 'contain',
                    'flex' => 'flex',
                ],
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BORDER_RADIUS,
                SettingField::LABEL => __('Border radius', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_NUMBER,
                SettingField::SIZE => 'small',
                SettingField::DEFAULT => '',
                SettingField::SANITIZE => 'int',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BORDER_SIZE,
                SettingField::LABEL => __('Border size', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_NUMBER,
                SettingField::SIZE => 'small',
                SettingField::DEFAULT => '',
                SettingField::SANITIZE => 'int',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BORDER_COLOR,
                SettingField::LABEL => __('Border color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BOX_SHADOW,
                SettingField::LABEL => __('Box shadow', 'custom-login'),
                SettingField::DESC => sprintf(
                    __(
                        'Use <a href="%s" target="_blank">box shadow</a> syntax w/ out color. <code>inset h-shadow v-shadow blur spread</code>',
                        'custom-login'
                    ),
                    'https://www.w3schools.com/cssref/css3_pr_box-shadow.asp'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXT,
                SettingField::SIZE => 'medium',
                SettingField::DEFAULT => '5px 5px 10px',
            ],
            [
                SettingField::NAME => OptionKey::LOGIN_FORM_BOX_SHADOW_COLOR,
                SettingField::LABEL => __('Box shadow color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Miscellaneous', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::LABEL_COLOR,
                SettingField::LABEL => __('Label color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Below Form anchor', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::NAV_COLOR,
                SettingField::LABEL => __('Nav color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::NAV_TEXT_SHADOW_COLOR,
                SettingField::LABEL => __('Nav text-shadow color', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::NAV_HOVER_COLOR,
                SettingField::LABEL => __('Nav color hover', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::NAV_TEXT_SHADOW_HOVER_COLOR,
                SettingField::LABEL => __('Nav text-shadow hover', 'custom-login'),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_COLOR_ALPHA,
                SettingField::DEFAULT => '',
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Custom CSS', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::CUSTOM_CSS,
                SettingField::LABEL => '',
                SettingField::DEFAULT => '',
                SettingField::DESC => sprintf(
                    '%s %s',
                    __('Allowed variables:', 'custom-login'),
                    '<ul>
			<li>{BSLASH} = "\" (backslash)</li>
			<li><a href="https://wordpress.org/support/topic/quotes-in-custom-css-gets-replaced-with-useless-quote?replies=4">Request others</a></li>
			</ul>'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::SANITIZE => fn($css): string => wp_specialchars_decode(wp_filter_nohtml_kses($css)),
                SettingField::ATTRIBUTES => [
                    'data-codemirror' => 'css',
                ],
            ],
            [
                SettingField::NAME => OptionKey::ANIMATE_CSS,
                SettingField::LABEL => __('Animate', 'custom-login'),
                SettingField::DESC => sprintf(
                    __('Include <a href="%s">animate.css</a>?', 'custom-login'),
                    'https://daneden.github.io/animate.css/'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
                SettingField::DEFAULT => 'off',
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Custom HTML', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::CUSTOM_HTML,
                SettingField::LABEL => '',
                SettingField::DEFAULT => '',
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::SANITIZE => 'wp_kses_post', //Allow HTML
                SettingField::ATTRIBUTES => [
                    'data-codemirror' => 'html',
                ],
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Custom Javascript', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::CUSTOM_JQUERY,
                SettingField::LABEL => '',
                SettingField::DEFAULT => '',
                SettingField::DESC => sprintf(
                    '<code>%1$s</code>&nbsp;%2$s&nbsp;<code>%3$s</code><br>',
                    esc_html('<script type="text/javascript">'),
                    __('Your custom javascript will output here', 'custom-login'),
                    esc_html('</script>')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_TEXTAREA,
                SettingField::SANITIZE => 'wp_specialchars_decode',
                SettingField::ATTRIBUTES => [
                    'data-codemirror' => 'javascript',
                ],
            ],
        ],
        Factory::getSection(Factory::SECTION_GENERAL) => [
            [
                SettingField::NAME => OptionKey::ACTIVE,
                SettingField::LABEL => __('Activate', 'custom-login'),
                SettingField::DESC => __('Allow Custom Login to hook into WordPress.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
                SettingField::DEFAULT => OptionValue::ON,
            ],
            [
                SettingField::NAME => OptionKey::CAPABILITY,
                SettingField::LABEL => __('Capability', 'custom-login'),
                SettingField::DESC => sprintf(
                    __(
                        'Set the minimum user capability to manage these settings. The default capability is <code>%s</code>',
                        'custom-login'
                    ),
                    'manage_options'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_SELECT,
                SettingField::SIZE => 'medium',
                SettingField::DEFAULT => 'manage_options',
                SettingField::OPTIONS => TheFrosty\CustomLogin\getWpRoles(),
            ],
            [
                SettingField::NAME => OptionKey::EXTENSIONS_MENU,
                SettingField::LABEL => __('Extensions Submenu', 'custom-login'),
                SettingField::DESC => __('Show (checked) or hide the extensions in the WordPress\' settings menu.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
                SettingField::DEFAULT => OptionValue::OFF,
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Tracking Settings', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::TRACKING,
                SettingField::LABEL => __('Usage tracking', 'custom-login'),
                SettingField::DESC => __(
                    'Allow Frosty Media to anonymously track how this plugin is used (and help us make the plugin better). Opt-in and receive a 20% discount code for all Custom Login extensions. Get your coupon code <a href="https://frosty.media/?p=21442">here</a>.',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Notices', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::ADMIN_NOTICES,
                SettingField::LABEL => __('Admin notices', 'custom-login'),
                SettingField::DESC => sprintf(
                    '%s %s',
                    __('Allow admin notices everywhere in WordPress.', 'custom-login'),
                    __('Unchecked equals "off" (do not allow).', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
                SettingField::DEFAULT => OptionValue::ON,
            ],
            [
                SettingField::NAME => OptionKey::DASHBOARD_WIDGET,
                SettingField::LABEL => __('Dashboard widget', 'custom-login'),
                SettingField::DESC => sprintf(
                    '%s %s',
                    __('Show a dashboard widget, like WordPress news for Frosty Media.', 'custom-login'),
                    __('Unchecked equals "off" (do not allow).', 'custom-login')
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
                SettingField::DEFAULT => OptionValue::ON,
            ],
            [
                SettingField::NAME => OptionKey::BREAK_S,
                SettingField::LABEL => sprintf('<h4>%s</h4>', __('Login functions', 'custom-login')),
                SettingField::DESC => '',
                SettingField::TYPE => FieldTypes::FIELD_TYPE_HTML,
            ],
            [
                SettingField::NAME => OptionKey::WP_SHAKE_JS,
                SettingField::LABEL => __('Disable Login shake', 'custom-login'),
                SettingField::DESC => __('Disable the login forms animated "shake" on error.', 'custom-login'),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
            [
                SettingField::NAME => OptionKey::REMOVE_LOGIN_CSS,
                SettingField::LABEL => __('Remove login CSS', 'custom-login'),
                SettingField::DESC => __(
                    'Remove WordPress\' login CSS. Warning: You\'ll have to add additional styles not set by this plugin.',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
            [
                SettingField::NAME => OptionKey::LOSTPASSWORD_TEXT,
                SettingField::LABEL => __('Remove lost password text', 'custom-login'),
                SettingField::DESC => __(
                    'Remove the "Lost Password?" text. This does <strong>not</strong> disable the lost password function.',
                    'custom-login'
                ),
                SettingField::TYPE => FieldTypes::FIELD_TYPE_CHECKBOX,
            ],
        ],
    ],
];
