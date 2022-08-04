<?php declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
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

define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress_test');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wp');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASS') ?: 'password');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: '127.0.0.1');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY', ']$:,ddmsBSpY++_,~!MT+T)m+N@dZfRjGMXXG}VvDx<`_L}p<1A1Egejj$3TkETd');
define('SECURE_AUTH_KEY', 'q`M!posJii3GT{auH<kO-eUi#JJvQ/md&%I0Lvy%3~_M*OB)TuXh8TR.pI>C6z a');
define('LOGGED_IN_KEY', '$CRI0?|w`X/tVGz[}WF]B{]bE$DhE]0dg}jmmc`F0}QXT;]ib@GhHmeCG7ay <T<');
define('NONCE_KEY', 'W90c-$RIlL8xa<CN4[L?|MJqjfg0#S|D+;mbq ?/{lxrv0d<IKu&([`{Lpr3}@-V');
define('AUTH_SALT', 'Z^Qc!XEV#9<i#!Aw{d%6V*f$A0<Z8E>[}H?>-NdsTf.KFjQ~9@DE+m0D QMj1-!+');
define('SECURE_AUTH_SALT', 'oK%>WO`VV?p&OMAT6mk>U;#HT7%QnQ3I{W|LG~nxdW@KWaM,r&+;^8-f^J=QPg~,');
define('LOGGED_IN_SALT', 'MOXV/qP< ML|&eZYuv6rp$(RW OK}Hp$SwhCqY%T5^XjKs0YQV]imsbNxp>|n|1&');
define('NONCE_SALT', 'L3.( +bK+PG[7C{YkXLZlg]SBCLx[5&s3PPV]x)AZ-1!$y%-#SDNt#,T<~aB+SQ6');


$table_prefix = 'wptests_';

define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_TESTS_TITLE', 'Test Blog');

define('WP_PHP_BINARY', 'php');

define('WPLANG', '');
