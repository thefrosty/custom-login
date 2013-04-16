<?php

/* Setup the plugin */
$login = CUSTOMLOGIN();

if ( !$login->is_active() )
	return;

global $cl_js_atts;

extract( $cl_js_atts, EXTR_SKIP );

/* Cache ALL THE THINGS! */
$js = wp_cache_get( $login->id . '_script' );

if ( false === $js ) :

	$js = '';
	
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) $js .= "/**\n *\n" . print_r( $cl_js_atts, true ) . " */\n\n";
		
	$js .= "
	/**
	 * Custom Login by Austin Passy
	 *
	 * Plugin URI	: http://austinpassy.com/wordpress-plugins/custom-login
	 * Version		: $version
	 * Author URI	: http://austinpassy.com
	 * Pro Version	: http://extendd.com/plugin/custom-login-pro
	 */\n\n";
	 
	$js .= 'jQuery(document).ready(function($) {';
	
	/* Custom user input */
	if ( !empty( $custom_jquery ) ) {
		
		$js .= "\n\t/* Custom JS */\n\t";
		$js .= wp_specialchars_decode( stripslashes( $custom_jquery ), 1, 0, 1 );
		$js .= "\n";
		
	}
	
	$js .= '});';

	/* WP Magic */
	wp_cache_set( $login->id . '_script', $js );
endif;

/* Out of the frying pan, and into the fire! */
echo $js;