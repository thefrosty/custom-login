<?php

/**
 * Weclome Page Class
 *
 * @package     Custom Login
 * @subpackage  Welcome Page
 * @copyright   Copyright (c) 2013, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Custom Login Welcome Page Class
 *
 * A general class for About and Credits page.
 *
 * @access      public
 * @since       2.0
 * @return      void
 */
class Custom_Login_Scripts_Styles {
	
	/** Singleton *************************************************************/
	private static $instance;

	/**
	 * Main Instance
	 *
	 * @staticvar 	array 	$instance
	 * @return 		The one true instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Helper function to convert HEX to RGB
	 *
	 * @ref		http://css-tricks.com/snippets/php/convert-hex-to-rgb/#comment-355641
	 * @return	array
	 */
	public function hex2rgb( $color ) {
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
	
	/**
	 * Helper function to convert RGB to HEX
	 *
	 * @ref		http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
	 * @return	string
	 */
	public function rgb2hex( $rgb ) {
	   $hex  = "#";
	   $hex .= str_pad( dechex( $rgb[0] ), 2, "0", STR_PAD_LEFT );
	   $hex .= str_pad( dechex( $rgb[1] ), 2, "0", STR_PAD_LEFT );
	   $hex .= str_pad( dechex( $rgb[2] ), 2, "0", STR_PAD_LEFT );
	
	   return $hex; // returns the hex value including the number sign (#)
	}
	
	/**
	 * Helper function to convert RGBA to HEX
	 *
	 * @ref		http://stackoverflow.com/questions/5798129/regular-expression-to-only-allow-whole-numbers-and-commas-in-a-string
	 * @return	string
	 */
	public function rgba2hex( $rgba ) {		
	//	$rgba = str_replace( array( 'rgb', 'rgba', '(', ')' ), '', $rgba );
		$rgba = preg_replace( 
			array(
				'/[^\d,]/',    // Matches anything that's not a comma or number.
				'/(?<=,),+/',  // Matches consecutive commas.
				'/^,+/',       // Matches leading commas.
				'/,+$/'        // Matches trailing commas.
			), '', $rgba );
		$rgba = explode( ',', $rgba );
		
		$hex  = "#";
		$hex .= str_pad( dechex( $rgba[0] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgba[1] ), 2, "0", STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgba[2] ), 2, "0", STR_PAD_LEFT );
		
		return $hex; // returns the hex value including the number sign (#)
	}
	
	/**
	 * Helper function to convert RGB(A) to array
	 *
	 * @return	bool
	 */
	public function is_rgba( $str ) {
		$is_rgba = strpos( $str, 'rgba' );
		
		if ( false === $is_rgba )
			return false;
		
		return true;
	}
	
	/**
	 * Browser prefixes
	 *
	 * @since	1.1 (1/8/13)
	 */
	public function prefixit( $input, $option ) {
		$prefixs = array( '-webkit-', '-moz-', '-ms-', '-o-', '' );
		
		$output  = "\n\t";
		
		foreach ( $prefixs as $prefix ) {
			$output .= $this->trailingsemicolonit( $prefix . $input . ': ' . esc_attr( $option ) );
		}
		
		return $output;
	}
		
	/**
	 * Add a semi colon
	 *
	 * Remove esc_attr since it's encoding single quotes in image urls with quotes.
	 * 
	 * @since	1.1 (1/8/13)
	 * @updated	1.1.1 (1/9/13)
	 */
	public function trailingsemicolonit( $input ) {
		$output  = rtrim( $input, ';' );
		$output .= ';' . "\n\t";
		
		return $output;
	}
	
	/**
	 * Open a new CSS rule
	 * 
	 * @since	2.0 
	 */
	public function cssrule( $rule ) {
		$output  = rtrim( $rule, '{' );
		$output .= " {\n\t";
		
		return $output;
	}
	
}

/**
 * Funky fresh.
 */
if ( !function_exists( 'CUSTOM_LOGIN_SCRIPT_STYLES' ) ) :
	function CUSTOM_LOGIN_SCRIPT_STYLES() {
		return Custom_Login_Scripts_Styles::instance();
	}
endif;