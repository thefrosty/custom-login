<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Roost
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Is Roost allowed?
if ( !defined( 'CL_ALLOW_ROOST' ) ) return;

/**
 * Roost (push notifications) signup
 *
 * @access public
 * @since  3.2
 * @return void
 */
class CL_Roost {

	/**
	 * Roost API Key.
	 *
	 * @access private
	 */
	private $api_key;

	/**
	 * Get things going
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		
		$this->api_key = '7fe37450d6104fd8929240faa803caf1';
		
		add_action( 'admin_action_' . CUSTOM_LOGIN_OPTION . '_allow_push',	array( $this, 'admin_action_hook' ) );
		add_action( CUSTOM_LOGIN_OPTION . '_settings_sidebars',				array( $this, 'settings_sidebar' ), 23 );
	}

	/**
	 * Action hook called on 'admin_action_' $_REQUEST
	 *
	 * @return	void
	 */
	public function admin_action_hook() {
		
		add_action( 'admin_head',	array( $this, 'admin_head' ), 99 );		
	}

	/**
	 * Output our script to the admin header.
	 *
	 * @ref		https://goroost.com/push-notification-documentation#document-2
	 * @return	void
	 */
	public function admin_head() {
		echo '<script src="https://cdn.goroost.com/roostjs/' . $this->api_key . '" async></script>';
		echo "<script>\n";
		echo "var _roost = _roost || [];\n";
		echo "_roost.push([\"segments_add\", \"customlogin\"]);\n";
		echo "</script>\n";
	}
	
	/**
	 * Box with a link to output our script.
	 *
	 * @return	string
	 */
	public function settings_sidebar( $args ) {
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if ( !preg_match('/macintosh|mac os x/i', $user_agent ) )
			return;
		
		if ( !preg_match('/safari/i', $user_agent ) )
			return;
		
		$content = sprintf( __( 'Stay up to date with desktop push notifications: <a href="%s">click here</a>, then click "allow".', CUSTOM_LOGIN_DIRNAME ), esc_url( add_query_arg( 'action', sprintf( '%s_allow_push', CUSTOM_LOGIN_OPTION ), admin_url() ) ) );
		
		CUSTOMLOGIN()->settings_api->postbox( 'custom-login-roost', sprintf( __( '%sPush Notifications', CUSTOM_LOGIN_DIRNAME ), '*' ), $content );
	}

}
new CL_Roost;