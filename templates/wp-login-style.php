<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !defined( 'SHORTINIT' ) ) define( 'SHORTINIT', true );

/* Setup the plugin */
$login = CUSTOMLOGIN();

if ( !$login->is_active() )
	return;

global $cl_css_atts;

/* Extract */
extract( $cl_css_atts, EXTR_SKIP );

/* Cache ALL THE THINGS! */
if ( false === ( $css = get_transient( $login->id . '_style' ) ) ) :

	$css = '';
	$close_rule = "}\n";
	
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) $css .= "/**\n *\n" . print_r( $cl_css_atts, true ) . " */\n\n";
		
	$css .= "
/**
 * Custom Login by Austin Passy
 *
 * Plugin URI	: http://austinpassy.com/wordpress-plugins/custom-login
 * Version		: $version
 * Author URI	: http://austinpassy.com
 * Extensions	: http://extendd.com/plugins/tag/custom-login-extension/
 */\n\n";
	
	/* Custom user input */
	if ( !empty( $custom_css ) ) {
		
		$css .= "/* Custom CSS */\n";
		$css .= wp_specialchars_decode( stripslashes( $custom_css ), 1, 0, 1 );
		$css .= "\n\n";
		
	}
	
	/**
	 * Open html
	 *
	 * @rule	html
	 */
	$css .= cssrule( 'html' );
		
		if ( !empty( $html_background_color ) && 'on' === $html_background_color_checkbox ) {
			
			$color = hex2rgb( $html_background_color );
			$css .= trailingsemicolonit( "background-color: rgba({$color['red']},{$color['green']},{$color['blue']},{$html_background_color_opacity})" );
			
		} elseif( !empty( $html_background_color ) ) {
			
			$css .= trailingsemicolonit( "background-color: {$html_background_color}" );
			
		}
			
		if ( !empty( $html_background_url ) ) {
			
			$css .= trailingsemicolonit( "background-image: url('{$html_background_url}')" );
			$css .= trailingsemicolonit( "background-position: {$html_background_position}" );
			$css .= trailingsemicolonit( "background-repeat: {$html_background_repeat}" );
		
			if ( !empty( $html_background_size ) && 'none' !== $html_background_size ) {
				
				$html_background_size = ( 'flex' !== $html_background_size ) ? $html_background_size : '100% auto';
				$css .= prefixit( 'background-size', $html_background_size );
				
			}
		}
	
	/* CLOSE html */
	$css .= $close_rule;
	
	/**
	 * Open body.login
	 *
	 * @rule	body.login
	 */
	if ( !empty( $html_background_color ) || !empty( $html_background_url ) ) {
		
		$css .= cssrule( 'body.login' );
		$css .= trailingsemicolonit( "background: transparent" );
	
		/* CLOSE body */
		$css .= $close_rule;	
		
	}
	
	/**
	 * Open login form
	 *
	 * @rule	#login form
	 */
	$css .= cssrule( '#login form' );
	
		if ( !empty( $login_form_background_color ) && 'on' === $login_form_background_color_checkbox ) {
			
			$color = hex2rgb( $login_form_background_color );
			$css .= trailingsemicolonit( "background-color: rgba({$color['red']},{$color['green']},{$color['blue']},{$login_form_background_color_opacity})" );
			
		} elseif( !empty( $login_form_background_color ) ) {
			
			$css .= trailingsemicolonit( "background-color: {$login_form_background_color}" );
			
		}
		if ( !empty( $login_form_background_url ) ) {
			
			$css .= trailingsemicolonit( "background-image: url('{$login_form_background_url}')" );
			$css .= trailingsemicolonit( "background-position: {$login_form_background_position}" );
			$css .= trailingsemicolonit( "background-repeat: {$login_form_background_repeat}" );
		
			if ( !empty( $login_form_background_size ) && 'none' != $login_form_background_size ) {
				
				$login_form_background_size = ( 'flex' != $login_form_background_size ) ? $login_form_background_size : '100% auto';
				$css .= prefixit( 'background-size', $login_form_background_size );
				
			}
			
		}
		
		if ( !empty( $login_form_border_size ) && !empty( $login_form_border_color ) ) {
			
			$login_form_border_size = rtrim( $login_form_border_size, 'px' );
			$css .= trailingsemicolonit( "border: {$login_form_border_size}px solid {$login_form_border_color}" );
			
		}
		
		if ( !empty( $login_form_border_radius ) ) {
			
			$login_form_border_radius = rtrim( $login_form_border_radius, 'px' ) . 'px';
			$css .= prefixit( 'border-radius', $login_form_border_radius );
			
		}
		
		if ( !empty( $login_form_box_shadow ) ) {
			
			$box_shadow = $login_form_box_shadow . ' ' . $login_form_box_shadow_color;	
			$css .= prefixit( 'box-shadow', trim( $box_shadow ) );
			
		}
	
	/* CLOSE login form */
	$css .= $close_rule;
	
	/**
	 * Open login h1
	 *
	 * @rule	#login h1
	 */
	if ( ( !empty( $hide_wp_logo ) && 'on' === $hide_wp_logo ) && empty( $logo_background_url ) ) {
		
		$css .= cssrule( '#login h1' );
		$css .= trailingsemicolonit( 'display: none' );
	
		/* CLOSE login h1 */
		$css .= $close_rule;
	
	}
	
	/**
	 * Open login h1 a
	 *
	 * @rule	#login h1 a
	 */
	if ( !empty( $logo_background_url ) ) {
	
		$css .= cssrule( '#login h1 a' );
	
		$css .= trailingsemicolonit( "background-image: url('{$logo_background_url}')" );
		$css .= trailingsemicolonit( "background-position: {$logo_background_position}" );
		$css .= trailingsemicolonit( "background-repeat: {$logo_background_repeat}" );
			
		if ( !empty( $logo_background_size ) && 'none' != $logo_background_size ) {
			
			$logo_background_size = ( 'flex' != $logo_background_size ) ? $logo_background_size : '100% auto';
			$css .= prefixit( 'background-size', $logo_background_size );
			
		}
		
		/* CLOSE login h1 a */
		$css .= $close_rule;
		
	}
	
	/**
	 * Open form label
	 *
	 * @rule	#loginform label
	 */
	if ( !empty( $label_color ) ) {	
		
		$css .= cssrule( '#loginform label' );
		
		if ( 'on' === $label_color_checkbox ) {
			
			$color = hex2rgb( $label_color );
			$css .= trailingsemicolonit( "color: rgba({$color['red']},{$color['green']},{$color['blue']},{$label_color_opacity})" );
			
		} else {
		
			$css .= trailingsemicolonit( "color: {$label_color}" );
			
		}
		
		/* CLOSE login h1 a */
		$css .= $close_rule;
		
	}
	
	/**
	 * Open below form links
	 *
	 * @rule	.login #nav a, .login #backtoblog a
	 */
	if ( !empty( $nav_color ) ) {	
		
		$css .= cssrule( '.login #nav a, .login #backtoblog a' );
		
		if ( 'on' === $nav_color_checkbox ) {
			
			$color = hex2rgb( $nav_color );
			$css .= trailingsemicolonit( "color: rgba({$color['red']},{$color['green']},{$color['blue']},{$nav_color_opacity}) !important" );
			
		} else {
		
			$css .= trailingsemicolonit( "color: {$nav_color} !important" );
			
		}
		
		if ( !empty( $nav_text_shadow_color ) && 'on' === $nav_text_shadow_color_checkbox ) {
			
			$color = hex2rgb( $nav_text_shadow_color );
			$css .= trailingsemicolonit( "text-shadow: 0 1px 0 rgba({$color['red']},{$color['green']},{$color['blue']},{$nav_text_shadow_color_opacity})" );
			
		} elseif( !empty( $nav_text_shadow_color ) ) {
		
			$css .= trailingsemicolonit( "text-shadow: 0 1px 0 {$nav_text_shadow_color}" );
			
		}
		
		/* CLOSE login h1 a */
		$css .= $close_rule;
		
	}
	
	/**
	 * Open below form links :hover
	 *
	 * @rule	.login #nav a:hover, .login #backtoblog a:hover
	 */
	if ( !empty( $nav_hover_color ) ) {	
		
		$css .= cssrule( '.login #nav a:hover, .login #backtoblog a:hover' );
		
		if ( 'on' === $nav_hover_color_checkbox ) {
			
			$color = hex2rgb( $nav_hover_color );
			$css .= trailingsemicolonit( "color: rgba({$color['red']},{$color['green']},{$color['blue']},{$nav_hover_color_opacity}) !important" );
			
		} else {
		
			$css .= trailingsemicolonit( "color: {$nav_hover_color} !important" );
			
		}
		
		if ( !empty( $nav_text_shadow_hover_color ) && 'on' === $nav_text_shadow_hover_color_checkbox ) {
			
			$color = hex2rgb( $nav_text_shadow_hover_color );
			$css .= trailingsemicolonit( "text-shadow: 0 1px 0 rgba({$color['red']},{$color['green']},{$color['blue']},{$nav_text_shadow_hover_color_opacity})" );
			
		} elseif( !empty( $nav_text_shadow_hover_color ) ) {
		
			$css .= trailingsemicolonit( "text-shadow: 0 1px 0 {$nav_text_shadow_hover_color}" );
			
		}
		
		/* CLOSE login h1 a */
		$css .= $close_rule;
		
	}
	
	/* WP Magic */
	set_transient( $login->id . '_style', $css, YEAR_IN_SECONDS ); // Cache for a year
endif;

/* Out of the frying pan, and into the fire! */
echo $css;