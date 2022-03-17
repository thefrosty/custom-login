<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\CustomLogin\Settings\Api\Factory;
use function add_action;
use function function_exists;
use function get_editable_roles;
use function is_array;
use function preg_match;
use function sanitize_key;
use function sprintf;
use const DAY_IN_SECONDS;

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
