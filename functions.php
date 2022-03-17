<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\CustomLogin\Settings\Api\Factory;
use function add_action;
use function function_exists;
use function get_editable_roles;
use function is_array;
use function preg_match;
use function sanitize_hex_color_no_hash;
use function sanitize_key;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use const DAY_IN_SECONDS;
use const STR_PAD_LEFT;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return all editable role capabilities.
 * @return array<string, string>
 */
function getWpRoles(): array
{
    $key = sprintf('%1$s%2$s', Factory::PREFIX, sanitize_key(__FUNCTION__));
    $roles = get_transient($key);
    if (empty($roles)) {
        add_action('shutdown', static function () use ($key, &$roles): void {
            $roles = _getEditableRoles();
            set_transient($key, $roles, DAY_IN_SECONDS);
        });
    }

    return !is_array($roles) ? ['manage_options' => 'manage_options'] : $roles;
}

/**
 * Helper function to convert HEX to RGB
 * @ref http://css-tricks.com/snippets/php/convert-hex-to-rgb/#comment-355641
 * @param string $color
 * @return array|null
 */
function hex2rgb(string $color): ?array
{
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }
    if (strlen($color) === 6) {
        [$red, $green, $blue] = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
    } elseif (strlen($color) === 3) {
        [$red, $green, $blue] = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
    } else {
        return null;
    }

    return ['red' => hexdec($red), 'green' => hexdec($green), 'blue' => hexdec($blue)];
}

/**
 * Helper function to convert RGB to HEX.
 * @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
 * @param array<int> $rgb
 * @return string
 */
function rgb2hex(array $rgb): string
{
    $hex = str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
    $hex .= str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
    $hex .= str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

    return sprintf('#%1$s', sanitize_hex_color_no_hash($hex));
}

/**
 * Helper function to convert RGBA to HEX.
 * @ref http://stackoverflow.com/questions/5798129/regular-expression-to-only-allow-whole-numbers-and-commas-in-a-string
 * @param string $rgba
 * @return string
 */
function rgba2hex(string $rgba): string
{
    $rgba = explode(
        ',',
        preg_replace(
            [
                '/[^\d,]/',    // Matches anything that's not a comma or number.
                '/(?<=,),+/',  // Matches consecutive commas.
                '/^,+/',       // Matches leading commas.
                '/,+$/'        // Matches trailing commas.
            ],
            '',
            $rgba
        )
    );

    return sprintf('#%1$s', sanitize_hex_color_no_hash(rgb2hex($rgba)));
}

/**
 * Is the current value an RGBA string?
 * @param string $value
 * @return bool
 */
function isRgba(string $value): bool
{
    return strpos($value, 'rgba');
}

/**
 * Browser prefixes.
 * @param string $property
 * @param string $value
 * @return string
 * @since 1.1 (1/8/13)
 */
function prefixIt(string $property, string $value): string
{
    $output = "\n\t";
    foreach (['-webkit-', '-moz-', ''] as $prefix) {
        $output .= trailingSemicolonIt(sprintf('%1$s%2$s', $prefix, $property), $value);
    }

    return $output;
}

/**
 * Add a Trailing Semicolon.
 * @param string $property
 * @param string $value
 * @return string
 * @since 1.1 (1/8/13)
 * @updated 1.1.1 (1/9/13) Remove esc_attr since it's encoding single quotes in image urls with quotes.
 */
function trailingSemicolonIt(string $property, string $value): string
{
    return sprintf("%s: %s;\n\t", $property, rtrim($value, ';'));
}

/**
 * Open a new CSS rule.
 * @param string $value
 * @return string
 * @since 2.0
 */
function openCssRule(string $value): string
{
    return sprintf("%s {\n\t", rtrim($value, '{'));
}

/**
 * Return all editable role capabilities.
 * @link http://codex.wordpress.org/Function_Reference/get_editable_roles
 * @access private
 * @return array<string, string>
 */
function _getEditableRoles(): array
{
    $roles = [];
    $get_editable_roles = !function_exists('get_editable_roles') ? null : get_editable_roles();
    if (empty($get_editable_roles)) {
        return ['manage_options' => 'manage_options'];
    }
    foreach ($get_editable_roles as $role) {
        /*
         * Avoid "Invalid argument supplied for foreach()".
         * @link https://wordpress.org/support/topic/invalid-argument-supplied-for-foreach-error-line-in-wp-dashboard?replies=2#post-6427631
         */
        if (!isset($role['capabilities']) || !is_array($role['capabilities'])) {
            continue;
        }
        foreach ($role['capabilities'] as $capability => $array) {
            // Remove the (deprecated) capabilities from the array
            if (preg_match('/^level_/', $capability)) {
                continue;
            }
            $roles[$capability] = $capability;
        }
    }

    return $roles;
}
