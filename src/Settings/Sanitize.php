<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Api\SettingSection;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use function apply_filters;
use function array_column;
use function array_filter;
use function array_intersect_key;
use function array_flip;
use function in_array;
use function current_user_can;
use function esc_url_raw;
use function is_array;
use function is_scalar;
use function preg_replace;
use function str_ends_with;
use function str_replace;
use function strtr;
use function wp_specialchars_decode;

/**
 * Sanitization helpers for settings that end up printed on wp-login.php.
 * @package TheFrosty\CustomLogin\Settings
 */
class Sanitize
{

    /**
     * Option keys of the design section that hold free-form CSS/HTML/JS, which must not be run through
     * the generic CSS value sanitizer.
     */
    private const array RAW_KEYS = [
        OptionKey::CUSTOM_CSS,
        OptionKey::CUSTOM_HTML,
        OptionKey::CUSTOM_JQUERY,
    ];

    /**
     * Sanitize callback for the custom javascript setting.
     * Only users who may post unfiltered HTML can change the value, otherwise the stored value is kept.
     * @param mixed $value
     * @return string
     */
    public static function customJavascript(mixed $value): string
    {
        if (!current_user_can('unfiltered_html')) {
            $current = Options::getOption(OptionKey::CUSTOM_JQUERY, Factory::getSection(Factory::SECTION_DESIGN), '');

            return is_scalar($current) ? (string)$current : '';
        }

        return is_scalar($value) ? wp_specialchars_decode((string)$value) : '';
    }

    /**
     * Make a CSS string safe to print inside a `<style>` element.
     * A literal `<` is never valid CSS outside a string, and inside a string `\3C ` is the same character, so
     * replacing it removes any chance of closing the `<style>` element (`</style>`) without changing the result.
     * @param string $css
     * @return string
     */
    public static function cssForStyleElement(string $css): string
    {
        return str_replace('<', '\3C ', $css);
    }

    /**
     * Sanitize a single CSS value (color, size, position, repeat, box-shadow...).
     * Removes every character that could end the declaration, the rule, or the style element.
     * @param mixed $value
     * @return string
     */
    public static function cssValue(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return (string)preg_replace('/[^#a-zA-Z0-9(),.%\s\-]/', '', (string)$value);
    }

    /**
     * Sanitize a URL that will be used inside `url('...')`.
     * @param mixed $value
     * @return string
     */
    public static function cssUrl(mixed $value): string
    {
        if (!is_scalar($value) || (string)$value === '') {
            return '';
        }

        return strtr(esc_url_raw((string)$value), [
            "'" => '%27',
            '"' => '%22',
            '(' => '%28',
            ')' => '%29',
            '\\' => '%5C',
            ' ' => '%20',
        ]);
    }

    /**
     * Sanitize the design options before they are used to build the login page stylesheet.
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public static function designOptionsForCss(array $options): array
    {
        foreach ($options as $key => $value) {
            if (in_array($key, self::RAW_KEYS, true) || is_array($value)) {
                continue;
            }
            $options[$key] = str_ends_with((string)$key, '_url') ? self::cssUrl($value) : self::cssValue($value);
        }

        return $options;
    }

    /**
     * Get the option names that may be written by a settings import or style pack.
     * @return string[]
     */
    public static function importableOptions(): array
    {
        $sections = array_column(CustomLogin::getSettings()['sections'] ?? [], SettingSection::SECTION_ID);

        /**
         * Filter the option names that a Custom Login import is allowed to update.
         * Extensions that register their own settings section can add their option name here.
         * @param string[] $sections
         */
        return array_filter((array)apply_filters('custom_login/importable_options', $sections), 'is_string');
    }

    /**
     * Reduce an import payload to the plugin's own options, and keep the stored custom javascript
     * when the current user may not post unfiltered HTML.
     * @param array<string, mixed> $import
     * @return array<string, array<string, mixed>>
     */
    public static function importPayload(array $import): array
    {
        $import = array_filter(
            array_intersect_key($import, array_flip(self::importableOptions())),
            'is_array'
        );

        $design = Factory::getSection(Factory::SECTION_DESIGN);
        if (isset($import[$design]) && !current_user_can('unfiltered_html')) {
            $import[$design][OptionKey::CUSTOM_JQUERY] = self::customJavascript(null);
        }

        return $import;
    }
}
