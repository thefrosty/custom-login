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
	
		foreach ( $role['capabilities'] as $capability => $array ) :
			
			// Remove the (deprecated) capabilities from the array
			if ( preg_match( '/^level_/', $capability ) )
				break;
				
			$roles[$capability] = $capability;
		endforeach;
		
	endforeach;
	
	return $roles;
}