<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'SHORTINIT' ) ) define( 'SHORTINIT', true );

global $cl_js_atts;

extract( $cl_js_atts, EXTR_SKIP );

/* Cache ALL THE THINGS! */
if ( false === ( $js = get_transient( $trans_key ) ) ) :

	$js = '';
	
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) $js .= "/**\n *\n" . print_r( $cl_js_atts, true ) . " */\n\n";
		
	$js .= "
/**
 * Custom Login by Austin Passy
 *
 * Plugin URI  : https://frosty.media/plugins/custom-login/
 * Version     : $version
 * Author URI  : http://austin.passy.co/
 * Extensions  : https://frosty.media/plugin/tag/custom-login-extension/
 */\n\n";
	 
	$js .= '(function($) {
"use strict";';
	
	/* Custom user input */
	if ( !empty( $custom_jquery ) ) {
		
		$js .= "\n\n/* Custom JS */\n";
		$js .= wp_specialchars_decode( stripslashes( $custom_jquery ), 1, 0, 1 );
		$js .= "\n\n";
		
	}
	
	$js .= '}(jQuery));';

	/* WP Magic */
	set_transient( $trans_key, $js, YEAR_IN_SECONDS/2 ); // Cache for six months
endif;

/* Out of the frying pan, and into the fire! */
echo $js;