<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'SHORTINIT' ) ) define( 'SHORTINIT', true );

/* Setup the plugin */
$login = CUSTOMLOGIN();

if ( !$login->is_active() )
	return;

global $cl_js_atts;

extract( $cl_js_atts, EXTR_SKIP );

/* Cache ALL THE THINGS! */
if ( false === ( $js = get_transient( $login->id . '_script' ) ) ) :

	$js = '';
	
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) $js .= "/**\n *\n" . print_r( $cl_js_atts, true ) . " */\n\n";
		
	$js .= "
	/**
	 * Custom Login by Austin Passy
	 *
	 * Plugin URI	: http://austin.passy.co/wordpress-plugins/custom-login
	 * Version		: $version
	 * Author URI	: http://austin.passy.co
	 * Pro Version	: https://extendd.com/plugin/custom-login-pro
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
	set_transient( $login->id . '_script', $js, YEAR_IN_SECONDS ); // Cache for a year
endif;

/* Out of the frying pan, and into the fire! */
echo $js;