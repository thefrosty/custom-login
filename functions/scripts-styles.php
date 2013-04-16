<?php

/**
 * Helper function to convert HEX to RGB
 *
 * @ref		http://css-tricks.com/snippets/php/convert-hex-to-rgb/#comment-355641
 * @return	array
 */
if ( !function_exists( 'hex2rgb' ) ) :
	function hex2rgb( $color ) {
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}
		if ( strlen( $color ) == 6 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}
endif;

/**
 * Browser prefixes
 *
 * @since	1.1 (1/8/13)
 */
if ( !function_exists( 'prefixit' ) ) :
	function prefixit( $input, $option ) {
		$prefixs = array( '-webkit-', '-moz-', '-ms-', '-o-', '' );
		
		$output  = "\n\t";
		
		foreach ( $prefixs as $prefix ) {
			$output .= trailingsemicolonit( $prefix . $input . ': ' . esc_attr( $option ) );
		}
		
		return $output;
	}
endif;
	
/**
 * Add a semi colon
 *
 * Remove esc_attr since it's encoding single quotes in image urls with quotes.
 * 
 * @since	1.1 (1/8/13)
 * @updated	1.1.1 (1/9/13)
 */
if ( !function_exists( 'trailingsemicolonit' ) ) :
	function trailingsemicolonit( $input ) {
		$output  = rtrim( $input, ';' );
		$output .= ';' . "\n\t";
		
		return $output;
	}
endif;

/**
 * Open a new CSS rule
 * 
 * @since	2.0 
 */
if ( !function_exists( 'cssrule' ) ) :
	function cssrule( $rule ) {
		$output  = rtrim( $rule, '{' );
		$output .= " {\n\t";
		
		return $output;
	}
endif;