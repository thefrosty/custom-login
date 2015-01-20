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
include_once( trailingslashit( CUSTOM_LOGIN_DIR ) . 'includes/class-cl-common.php' );

// Delete all plugin options
foreach ( $sections as $section ) {
	delete_option( $section['id'] );
}

// Delete user meta data
$all_user_ids = get_users( 'fields=ID' );
foreach ( $all_user_ids as $user_id ) {
    delete_user_meta( $user_id, CUSTOM_LOGIN_OPTION . '_ignore_announcement' );
}

// Delete all announcement options and transients
delete_transient( CL_Common::get_transient_key( 'announcement' ) );			
delete_option( CUSTOM_LOGIN_OPTION . '_announcement_message' );

// Delete tracking options
delete_option( 'custom_login_tracking_last_send' );
delete_option( 'custom_login_hide_tracking_notice' );

// Cleanup Cron Events
wp_clear_scheduled_hook( 'custom_login_daily_scheduled_events' );
wp_clear_scheduled_hook( 'custom_login_weekly_scheduled_events' );

// Delete version option
delete_option( CUSTOM_LOGIN_OPTION . '_version' );