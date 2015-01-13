<?php
/**
 * Uninstall Custom Login
 *
 * @package     CustomLogin
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load Custom Login
include_once( 'custom-login.php' );
include_once( trailingslashit( CUSTOM_LOGIN_DIR ) . 'includes/default-settings.php' );

/** Delete all the Plugin Options */
foreach ( $sections as $section ) {
	delete_option( $section['id'] );
}

delete_option( CUSTOM_LOGIN_OPTION . '_announcement_message' );
delete_option( CUSTOM_LOGIN_OPTION . '_version' );
delete_option( 'cl_tracking_last_send' );
delete_option( 'cl_tracking_notice' );

/** Cleanup Cron Events */
wp_clear_scheduled_hook( 'cl_daily_scheduled_events' );
wp_clear_scheduled_hook( 'cl_weekly_scheduled_events' );