<?php
/**
 * Check whether Custom Login is sent to the WordPress.org plugin update check.
 *
 * Runs a sample `api.wordpress.org/plugins/update-check/` request through the `http_request_args` filter, the
 * way `wp_update_plugins()` does, so wp-utilities' DisablePluginUpdateCheck (registered by custom-login.php for
 * Composer installs) is exercised end to end.
 *
 * Usage: wp eval-file tests/ci/update-check.php <excluded|included>
 *   - excluded: Composer installs (no bundled vendor/), which are updated with Composer.
 *   - included: WordPress.org / GitHub release zips (bundled vendor/), which keep WordPress.org updates.
 *
 * Kept PHP 7.4 compatible: the PHP 7.4 package workflow runs it with PHP 7.4.
 * phpcs:disable PSR1.Files.SideEffects
 */

if (!defined('WP_CLI') || !WP_CLI) {
    exit(1);
}

const CUSTOM_LOGIN_CI_EXPECT_EXCLUDED = 'excluded';
const CUSTOM_LOGIN_CI_EXPECT_INCLUDED = 'included';
const CUSTOM_LOGIN_CI_UPDATE_CHECK_URL = 'https://api.wordpress.org/plugins/update-check/1.1/';
const CUSTOM_LOGIN_CI_OTHER_PLUGIN = 'hello.php';

$expect = $args[0] ?? '';
if (!in_array($expect, [CUSTOM_LOGIN_CI_EXPECT_EXCLUDED, CUSTOM_LOGIN_CI_EXPECT_INCLUDED], true)) {
    WP_CLI::error(sprintf(
        'Usage: wp eval-file %s <%s|%s>',
        basename(__FILE__),
        CUSTOM_LOGIN_CI_EXPECT_EXCLUDED,
        CUSTOM_LOGIN_CI_EXPECT_INCLUDED
    ));
}

if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$basename = '';
foreach (array_keys(get_plugins()) as $plugin_file) {
    if (basename($plugin_file) === 'custom-login.php') {
        $basename = $plugin_file;
        break;
    }
}
if ($basename === '' || !is_plugin_active($basename)) {
    WP_CLI::error('Custom Login is not installed and active.');
}

// The body WordPress sends: every installed plugin's headers, keyed by basename (see wp_update_plugins()).
$plugins = [
    'plugins' => [
        $basename => ['Name' => 'Custom Login', 'Version' => '1.0.0'],
        CUSTOM_LOGIN_CI_OTHER_PLUGIN => ['Name' => 'Hello Dolly', 'Version' => '1.0.0'],
    ],
    'active' => [$basename],
];
$request = apply_filters(
    'http_request_args',
    ['body' => ['plugins' => wp_json_encode($plugins)]],
    CUSTOM_LOGIN_CI_UPDATE_CHECK_URL
);
$sent = json_decode($request['body']['plugins'] ?? '', true);
$sent_plugins = $sent['plugins'] ?? [];

if (!isset($sent_plugins[CUSTOM_LOGIN_CI_OTHER_PLUGIN])) {
    WP_CLI::error('Other plugins must still be sent to the update check.');
}

$included = isset($sent_plugins[$basename]);
if ($expect === CUSTOM_LOGIN_CI_EXPECT_EXCLUDED && $included) {
    WP_CLI::error(sprintf('%s is sent to the WordPress.org update check, but should be excluded.', $basename));
}
if ($expect === CUSTOM_LOGIN_CI_EXPECT_INCLUDED && !$included) {
    WP_CLI::error(sprintf('%s is missing from the WordPress.org update check, but should be included.', $basename));
}

WP_CLI::success(sprintf('%s is %s in the WordPress.org update check.', $basename, $expect));
