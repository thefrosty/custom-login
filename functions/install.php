<?php
/**
 * Install Function
 *
 * @package     Custom Login
 * @subpackage  Install Function
 * @copyright   Copyright (c) 2013, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install.
 *
 * @access      private
 * @since       1.0
 * @return      void
 */
function ap_custom_login_install() {

	// Setup the Custom Post Type
	ap_custom_login_post_types();

	// Clear the permalinks
	flush_rewrite_rules();
	
	$cl_settings_page = get_page_by_title( __( 'Custom Login Settings', 'custom-login' ), '', 'custom_login' );

	// Checks if the purchase page option exists
	if ( empty( $cl_settings_page ) ) {
	    
		// Settings Page
		$settings = wp_insert_post(
			array(
				'post_title'     => __( 'Custom Login Settings', 'custom-login' ),
				'post_content'   => '',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'custom_login',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			)
		);
		
//		update_option( 'custom_login_version', CUSTOM_LOGIN_VERSION );
	}

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
		return;

	// Add the transient to redirect
    set_transient( '_custom_login_activation_redirect', true, 30 );
}
register_activation_hook( CUSTOM_LOGIN_FILE, 'ap_custom_login_install' );