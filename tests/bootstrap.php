<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$_tests_dir = getenv('WP_TESTS_DIR') ?: getenv('WP_PHPUNIT__DIR');

if (!$_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

if (!file_exists($_tests_dir . '/includes/functions.php')) {
    echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
    exit(1);
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';
tests_add_filter('muplugins_loaded', function () {
    add_filter('wp_die_handler', static function (): void {
        throw new WPDieException();
    });
    require dirname(__DIR__) . '/custom-login.php';
});

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
