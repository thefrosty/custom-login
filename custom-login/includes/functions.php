<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return all editiable role capabilites.
 *
 * @ref		http://codex.wordpress.org/Function_Reference/get_editable_roles
 * @return array
 */
function custom_login_get_editable_roles() {
	
	$roles = array();
	foreach ( get_editable_roles() as $role_name => $role ) :
		
		// https://wordpress.org/support/topic/invalid-argument-supplied-for-foreach-error-line-in-wp-dashboard?replies=2#post-6427631
		if ( !is_array( $role['capabilities'] ) )
			break;
			
		foreach ( $role['capabilities'] as $capability => $array ) :
			
			// Remove the (deprecated) capabilities from the array
			if ( preg_match( '/^level_/', $capability ) )
				break;
				
			$roles[$capability] = $capability;
		endforeach;
		
	endforeach;
	
	return $roles;
}