<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\WpUtilities\Plugin\PluginInterface;
use function add_action;
use function function_exists;
use function get_editable_roles;
use function is_admin;
use function is_array;
use function is_string;
use function preg_match;
use function sanitize_key;
use function sprintf;
use function strpos;
use function wp_doing_ajax;
use const WEEK_IN_SECONDS;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Are we on the Custom Login settings page?
 * @param PluginInterface $plugin
 * @return bool
 */
function isSettingsPage(PluginInterface $plugin): bool
{
    return $GLOBALS['pagenow'] === 'options-general.php' &&
        isset($_GET['page']) &&
        strpos($plugin->getSlug(), $_GET['page']) !== false;
}

/**
 * Return all editable role capabilities.
 * @return array<string, string>
 */
function getWpRoles(): array
{
    $key = sprintf('%1$s%2$s', Factory::PREFIX, sanitize_key(__FUNCTION__));
    $roles = get_transient($key);
    if (empty($roles) && is_admin() && !wp_doing_ajax()) {
        add_action('shutdown', static function () use ($key, &$roles): void {
            $roles = _getEditableRoles();
            set_transient($key, $roles, WEEK_IN_SECONDS);
        });
    }

    return !is_array($roles) ? ['manage_options' => 'manage_options'] : $roles;
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
            if (is_string($capability) && preg_match('/^level_/', $capability)) {
                continue;
            }
            $roles[$capability] = $capability;
        }
    }

    return $roles;
}
