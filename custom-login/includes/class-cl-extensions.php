<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Extensions
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class CL_Extensions {
	
	var	$extensions = array(),
		$checkout_url = '',
		$menu_page;
	
	public function __construct() {
		
		$this->checkout_url = CUSTOM_LOGIN_API_URL . 'checkout/';
		
		add_action( CUSTOM_LOGIN_OPTION . '_settings_sidebars',	array( $this, 'settings_sidebar' ), 20 );
		add_action( 'admin_menu',								array( $this, 'admin_menu' ), 10 );
		add_action( 'admin_init',								array( $this, 'remote_install_client' ), 10 );
		
		$this->get_extensions();
	}
	
	/**
	 * Box with a link to the extensions page.
	 */
	function settings_sidebar( $args ) {
		
		$content = sprintf( __( 'Install Custom Login extensions on <a href="%s">this page</a> with a valid license key. <small>Purchase your license key by clicking the appropriate link below</small>.', CUSTOM_LOGIN_DIRNAME ), sprintf( admin_url( 'options-general.php?page=%s/extensions' ), CUSTOM_LOGIN_DIRNAME ) );
		
		CUSTOMLOGIN()->settings_api->postbox( 'custom-login-extensions', __( 'Extensions Installer', CUSTOM_LOGIN_DIRNAME ), $content );
	}
	
	public function admin_menu() {
		
		$this->menu_page = add_options_page(
			__( 'Custom Login Extensions', CUSTOM_LOGIN_DIRNAME ),
			__( 'Custom Login Extentions', CUSTOM_LOGIN_DIRNAME ),
			'install_plugins',
			sprintf( '%s/extensions', CUSTOM_LOGIN_DIRNAME ),
			array( $this, 'html' )
		);
		
		remove_submenu_page( 'options-general.php', sprintf( '%s/extensions', CUSTOM_LOGIN_DIRNAME ) );
		
		add_action( 'load-' . $this->menu_page, array( $this, 'load' ) );
	}
	
	public function load() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}
	
	public function admin_enqueue() {
		wp_enqueue_style( CUSTOM_LOGIN_DIRNAME, plugins_url( 'css/admin.css', CUSTOM_LOGIN_FILE ), false, CUSTOM_LOGIN_VERSION, 'screen' );
	}
	
	/**
	 * Load the remote installer on our setting page only.
	 *
	 * @updated	3.1
	 */
	public function remote_install_client() {
		
		if ( !CL_Common::is_settings_page() )
			return;
		
		if ( !class_exists( 'CL_Remote_Install_Client' ) )
			require_once( trailingslashit( CUSTOM_LOGIN_DIR ) . 'includes/libraries/edd-remote-install-client/EDD_Remote_Install_Client.php' );
		
		$cl_remote_install_client = new CL_Remote_Install_Client(
			trailingslashit( CUSTOM_LOGIN_API_URL ) . 'edd-sl-api/',
			'settings_page_custom-login/extensions', // HARD CODE IT!!! Get is with var_dump( get_current_screen() );
			array( 'skipplugincheck' => 0, 'url' => home_url() )
		);
	}
	
	private function get_extensions() {
		
		// Save this for a latter date since our checkin for our HTML data doesn't get called on init...
		$ext_url		= false; //add_query_arg( array( 'edd_action' => 'cl_announcements' ), trailingslashit( CUSTOM_LOGIN_API_URL ) . 'cl-checkin-api/' );
		$transient_key	= CL_Common::get_transient_key( 'extensions' );
		$extensions	= CL_Common::wp_remote_get( $ext_url, $transient_key, WEEK_IN_SECONDS, 'CustomLogin' );
		
		if ( $extensions ) {
			$this->extensions[] = $extensions->html;
		}
		else {
			/* Stealth Login */
			$this->extensions[] = array(
				'title'			=> 'Custom Login Stealth Login',
				'description'	=> 'Protect your wp-login.php page from brute force attacks.',
				'url'			=> 'https://frosty.media/plugins/custom-login-stealth-login/',
				'image'			=> 'https://i.imgur.com/mhuymPG.jpg',
				'links'			=> array(
					array( 
						'description'	=> 'Personal',
						'download_id'	=> '108',
						'price_id'		=> '1',
						'price'			=> '$35',
					),
					array( 
						'description'	=> 'Plus',
						'download_id'	=> '108',
						'price_id'		=> '2',
						'price'			=> '$95',
					),
					array( 
						'description'	=> 'Professional',
						'download_id'	=> '108',
						'price_id'		=> '3',
						'price'			=> '$195',
					),
				),
			);
			
			/* Page Template */
			$this->extensions[] = array(
				'title'			=> 'Custom Login Page Template',
				'description'	=> 'Add a login form to any WordPress page.',
				'url'			=> 'https://frosty.media/plugins/custom-login-page-template/',
				'image'			=> 'https://i.imgur.com/A0rzS9q.jpg',
				'links'			=> array(
					array( 
						'description'	=> 'Personal',
						'download_id'	=> '120',
						'price_id'		=> '1',
						'price'			=> '$35',
					),
					array( 
						'description'	=> 'Plus',
						'download_id'	=> '120',
						'price_id'		=> '2',
						'price'			=> '$95',
					),
					array( 
						'description'	=> 'Professional',
						'download_id'	=> '120',
						'price_id'		=> '3',
						'price'			=> '$195',
					),
				),
			);
			
			/* Login Redirects */
			$this->extensions[] = array(
				'title'			=> 'Custom Login Redirects',
				'description'	=> 'Manage redirects after logging in.',
				'url'			=> 'https://extendd.com/plugin/wordpress-login-redirects/',
				'image'			=> 'https://i.imgur.com/aNGoyAa.jpg',
				'links'			=> array(
					array( 
						'description'	=> 'Personal',
						'download_id'	=> '124',
						'price_id'		=> '1',
						'price'			=> '$35',
					),
					array( 
						'description'	=> 'Plus',
						'download_id'	=> '124',
						'price_id'		=> '2',
						'price'			=> '$95',
					),
					array( 
						'description'	=> 'Professional',
						'download_id'	=> '124',
						'price_id'		=> '3',
						'price'			=> '$195',
					),
				),
			);
			
			/* No Password */
			$this->extensions[] = array(
				'title'			=> 'Custom Login No Password',
				'description'	=> 'Allow users to login without a password.',
				'url'			=> 'https://frosty.media/plugins/custom-login-no-passowrd-login/',
				'image'			=> 'https://i.imgur.com/7SXIpi5.jpg',
				'links'			=> array(
					array( 
						'description'	=> 'Personal',
						'download_id'	=> '128',
						'price_id'		=> '1',
						'price'			=> '$35',
					),
					array( 
						'description'	=> 'Plus',
						'download_id'	=> '128',
						'price_id'		=> '2',
						'price'			=> '$95',
					),
					array( 
						'description'	=> 'Professional',
						'download_id'	=> '128',
						'price_id'		=> '3',
						'price'			=> '$195',
					),
				),
			);
			
		} // if
	}
	
	public function html() {
		
		$html  = '<div class="wrap">';
		$html .= '<h2>' . __( 'Available Custom Login Extensions' ) . '</h2>';
		$html .= '<form method="post" action="options.php">';
		$html .= '<div class="section">';
		
		foreach( $this->extensions as $key => $extension ) {
			$html .= '<div class="col span_1_of_3 eddri-addon">';			
				$html .= '<div class="eddri-addon-container">';
					$html .= '<div class="eddri-img-wrap">';					
						$html .= '<a href="' . esc_url( add_query_arg( array( 'utm_source' => 'wordpressorg', 'utm_medium' => 'custom-login', 'utm_campaign' => 'eddri' ), $extension['url'] ) ) . '" target="_blank"><img class="eddri-thumbnail" src="' . $extension['image'] . '"></a>';						
						$html .= '<p>' . $extension['description'] . '</p>';						
					$html .= '</div>';
					
					$html .= '<h3>' . $extension['title'] . '</h3>';
					$html .= '<span class="eddri-status">Not Installed</span>';
					$html .= '<a class="button" data-edd-install="' . $extension['title'] . '">Install</a>';
					$html .= '<a class="button show-if-not-purchased" data-toggle="purchase-links-' . $key . '" style="display:none">Purchase License</a>';
					$html .= '<div id="purchase-links-' . $key . '" style="display:none">';
					
						$html .= '<ul>';						
						foreach( $extension['links'] as $link ) {
							$html .= '<li>';
							$html .= $link['description'] . ' (' . $link['price'] . '): <a href="' . esc_url( add_query_arg( array( 'edd_action' => 'straight_to_gateway', 'download_id' => $link['download_id'], 'edd_options[price_id]' => $link['price_id'] ), $this->checkout_url ) ) . '">PayPal</a>';
							$html .= ' | ';
							$html .= '<a href="' . esc_url( add_query_arg( array( 'edd_action' => 'add_to_cart', 'download_id' => $link['download_id'], 'edd_options[price_id]' => $link['price_id'] ), $this->checkout_url ) ) . '">Credit Card</a>';
							$html .= '</li>';
						}						
						$html .= '</ul>';
						
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		} // foreach
		
		$html .= '</div>';
		$html .= '</form>';
		$html .= $this->footer_script();
		$html .= '</div>';
		
		echo $html;
	}
	
	function footer_script() {
		ob_start(); ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		setTimeout( function() {			
			// Remote API helper
			$('a[data-toggle]').on('click',function(e) {
				e.preventDefault();
				$('#' + $(this).data('toggle')).toggle();
			});
			
			// Show Purchase button
			$('a[data-edd-install]').each(function() {
				var $this = $(this);
				setTimeout( function() {
					if ( $this.prev('.eddri-status').text() === 'Not Installed' ) {
						$this.closest( $this.parent() ).children('a.button').hide();
						$this.closest( $this.parent() ).children('a.button.show-if-not-purchased').show();
					}
				}, 500 );
			});
			
		}, 1000 );
	});
</script><?php return ob_get_clean();
	}
	
}
$GLOBALS['cl_extensions'] = new CL_Extensions();