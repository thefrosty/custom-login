<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Import_Export
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Usage tracking
 *
 * @access public
 * @since  3.0.7
 * @return void
 */
class CL_Import_Export {

	/**
	 * The menu
	 *
	 * @access private
	 */
	private $menu_page;

	/**
	 * Get things going
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	
		add_action( CUSTOM_LOGIN_OPTION . '_settings_sidebars',					array( $this, 'settings_sidebar' ), 30 );
		add_action( 'admin_action_' . CUSTOM_LOGIN_OPTION . '_import_settings',	array( $this, 'import_settings' ) );
		add_action( 'admin_action_' . CUSTOM_LOGIN_OPTION . '_export_settings',	array( $this, 'export_settings' ) );
	}
	
	/**
	 * Box with a link to the extensions page.
	 */
	function settings_sidebar( $args ) {
		
		$import_url = wp_nonce_url(
			add_query_arg( array( 'action' => CUSTOM_LOGIN_OPTION . '_import_settings' ),
				admin_url( 'admin.php' )
			),
			'import',
			'cl_nonce'
		);
		$export_url = wp_nonce_url(
			add_query_arg( array( 'action' => CUSTOM_LOGIN_OPTION . '_export_settings' ),
				admin_url( 'admin.php' )
			),
			'export',
			'cl_nonce'
		);
		
		if ( CUSTOM_LOGIN_VERSION < '3.1.0' ) {
			$content  = __( 'Coming in version 3.1', CUSTOM_LOGIN_DIRNAME );
		}
		else {
			$content  = '<ul>';
			$content .= sprintf( __( '<li><a href="%s">Import</a></li>', CUSTOM_LOGIN_DIRNAME ), esc_url( $import_url ) );
			$content .= sprintf( __( '<li><textarea></textarea></li>', CUSTOM_LOGIN_DIRNAME ), esc_url( $import_url ) );
			$content .= '</ul>';
			$content .= '<div id="import-export-wrapper"></div>';
		}
		
		CUSTOMLOGIN()->settings_api->postbox( 'custom-login-import-export', __( 'Settings Import/Export', CUSTOM_LOGIN_DIRNAME ), $content );
	}
	
	/**
	 * Import some settings into Custom Login.
	 *
	 * @access public
	 * @return array
	 */
	function import_settings() {
		
		if ( !isset( $_GET['cl_nonce']) || !wp_verify_nonce( $_GET['cl_nonce'], 'import' ) ) {
		}
		
		wp_redirect( remove_query_arg( array( 'action', 'cl_nonce' ) ) );
		exit;
	}

	/**
	 * Export Custom Logins settings.
	 *
	 * @access public
	 * @return void
	 */
	function export_settings() {
		
		if ( !isset( $_GET['cl_nonce']) || !wp_verify_nonce( $_GET['cl_nonce'], 'import' ) ) {
		}
		
		wp_redirect( remove_query_arg( array( 'action', 'cl_nonce' ) ) );
		exit;
	}

}
$GLOBALS['cl_import_export'] = new CL_Import_Export;