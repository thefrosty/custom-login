<?php declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
// This is the install path as defined by `wordpress-install-dir` in composer.json
$abspath = defined('TRAVIS') || getenv('TRAVIS') ? '/wordpress/' : '/';
define('ABSPATH', dirname(__DIR__) . '/wordpress/');

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
define('WP_DEFAULT_THEME', 'default');

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define('WP_DEBUG', true);

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wp_phpunit_tests');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress_user');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASS') ?: 'mysql_password');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: '127.0.0.1');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY', 'CV,%WA8/D@9|X}<|WnFk>@+*3*vdW&}=!;$|:6d+@!:7zYE|)9,P#+~mH<H0xd-Q');
define('SECURE_AUTH_KEY', ':zfmSThnc?EV1[Fe)Nt`Sl12?hdbgxeo:>Vb:oHZbOC/HGvlbzQu+S8AE/75|AGR');
define('LOGGED_IN_KEY', 'uukVo-T@T3=U9-bVc+Q4kWFd+w9A/Tu_6 iy}l.)ii@uP:!h|D<-b<)4Z8H~-krb');
define('NONCE_KEY', '`UmT$5bN-(Xh9=CjA@1-:gNE~@i2pi-1RaO;400bqdzb7_xTc*ytt0,Z|HpbYWdz');
define('AUTH_SALT', '+0~=j8U1I(%,A>{6=W&Ev8(.[-ZS4n[8t@@c:}yD$m+2?n|tw2Kmb||_Sq|7UgU<');
define('SECURE_AUTH_SALT', '3OmX+9[;KGmFsOtv75]Akd6.%]`v1i^f(?n)Z,-T6I-w9CyF1Y[(5A3|;UJ$54Z_');
define('LOGGED_IN_SALT', '2AGl4xbW]~[@vW|e cP:V4R0ZOFM![BiX(5!p3A?d(f DbkTtth)T9>$/mn<W[J!');
define('NONCE_SALT', 'Q9b|w@/r%PDZ2?1%9fPk=5/6tq8n,>vK)Bq)|[4cRi06Z}{PIK!2<``=H6(X!SP[');

$table_prefix = 'wptests_';

define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.local');
define('WP_TESTS_TITLE', 'Test Custom Login');

define('WP_PHP_BINARY', 'php');

define('WPLANG', '');
