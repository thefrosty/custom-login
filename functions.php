<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use function function_exists;
use function is_array;
use function preg_match;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return all editable role capabilities.
 * @link http://codex.wordpress.org/Function_Reference/get_editable_roles
 * @return array
 */
function getEditableRoles(): array
{
    $roles = [];
    $get_editable_roles = !function_exists('\get_editable_roles') ? null : \get_editable_roles();
    if (empty($get_editable_roles)) {
        return $roles;
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
