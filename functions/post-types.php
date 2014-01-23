<?php
/**
 * Post Type Functions
 *
 * @package     Custom Login
 * @subpackage  Post Type Functions
 * @copyright   Copyright (c) 2013, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Setup Core Post Type
 *
 * @access      private
 * @since       2.0
 * @return      void
 */
function ap_custom_login_post_types() {
	/** Payment Post Type */
	$labels = array(
		'name' 				=> _x('Logins', 'post type general name', 'custom-login' ),
		'singular_name' 	=> _x('Login', 'post type singular name', 'custom-login' ),
		'add_new' 			=> __( 'Add New', 'custom-login' ),
		'add_new_item' 		=> __( 'Add New Login', 'custom-login' ),
		'edit_item' 		=> __( 'Edit Login', 'custom-login' ),
		'new_item' 			=> __( 'New Login', 'custom-login' ),
		'all_items' 		=> __( 'All Logins', 'custom-login' ),
		'view_item' 		=> __( 'View Login', 'custom-login' ),
		'search_items' 		=> __( 'Search Logins', 'custom-login' ),
		'not_found' 		=>  __( 'No Logins found', 'custom-login' ),
		'not_found_in_trash'=> __( 'No Logins found in Trash', 'custom-login' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'Custom Login', 'custom-login' )
	);

	$args = array(
		'labels' 			=> $labels,
		'public' 			=> false,
		'query_var' 		=> false,
		'rewrite' 			=> false,
		'capability_type' 	=> 'page',
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> false
	);
	register_post_type( 'custom_login', $args );
}
add_action( 'init', 'ap_custom_login_post_types', 75 );