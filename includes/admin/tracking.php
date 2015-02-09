<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Tracking
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
 * @since  3.0.0
 * @return void
 */
class CL_Tracking {

	/**
	 * The data to send to the FM site
	 *
	 * @access private
	 */
	private $data;
	private $option;
	private $api;

	/**
	 * Get things going
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		$this->option	= CUSTOM_LOGIN_OPTION . '_general';
		$this->api 		= CUSTOM_LOGIN_API_URL . 'cl-checkin-api/?edd_action=cl_checkin';
		
		if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
			$this->api	= str_replace( CUSTOM_LOGIN_API_URL, 'http://frosty.media.dev/', $this->api );
		}
		
		$this->schedule_send();
		
		register_activation_hook( CUSTOM_LOGIN_FILE,					array( $this, 'activate' ) );
		
		add_action( CUSTOM_LOGIN_OPTION . '_after_sanitize_options',	array( $this, 'check_for_settings_optin' ) );
		add_action( 'admin_action_cl_opt_into_tracking',				array( $this, 'check_for_optin' ) );
		add_action( 'admin_action_cl_opt_out_of_tracking',			array( $this, 'check_for_optout' ) );
	#	add_action( 'admin_notices',								array( $this, 'admin_notice' ) );
	}
	
	/**
	 * Runs on plugin install.
	 *
	 * @since		3.0.0
	 * @return		void
	 */
	function activate() {		
		$this->send_checkin( true, array( 'on_activation' => 'yes' ) );
	}

	/**
	 * Check if the user has opted into tracking
	 *
	 * @access private
	 * @return bool
	 */
	private function tracking_allowed() {
		$tracking = CL_Common::get_option( 'tracking', 'general', 'off' );
		
		if ( 'on' === $tracking )
			return true;
		
		return false;
	}

	/**
	 * Setup the data that is going to be tracked
	 *
	 * @access private
	 * @return void
	 */
	private function setup_data( $extra_data = array() ) {

		$data = array();

		$theme_data		= wp_get_theme();
		$theme			= $theme_data->Name . ' ' . $theme_data->Version;

		$data['url']		= home_url();
		$data['version']	= get_bloginfo( 'version' );
		$data['theme']	= $theme;
		$data['email']	= get_bloginfo( 'admin_email' );

		// Retrieve current plugin information
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins		= array_keys( get_plugins() );
		$active_plugins	= get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				// Remove active plugins from list so we can show active and inactive separately
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']		= $active_plugins;
		$data['inactive_plugins']	= $plugins;
		$data['post_count']			= wp_count_posts( 'post' )->publish;
		$data['cl_version']			= CUSTOM_LOGIN_VERSION;
		
		if ( is_array( $extra_data ) && !empty( $extra_data ) ) {
			foreach( $extra_data as $key => $value ) {
				$data[$key] = $value;
			}
		}

		$this->data = $data;
	}

	/**
	 * Send the data to the FM server
	 *
	 * @access private
	 * @return void
	 */
	public function send_checkin( $override = false, $extra_data = array() ) {
		
		if ( ! $this->tracking_allowed() && ! $override )
			return;

		// Send a maximum of once per week
		$last_send = $this->get_last_send();
		if ( $last_send && $last_send > strtotime( '-1 week' ) )
			return;

		$this->setup_data( $extra_data );

		$response = wp_remote_post( $this->api, array(
			'method'      => 'POST',
			'timeout'     => apply_filters( 'cl_wp_remote_post_timeout', (int) 15 ),
			'redirection' => 5,
			'body'        => $this->data,
			'user-agent'  => 'CustomLogin/' . CUSTOM_LOGIN_VERSION . '; ' . get_bloginfo( 'url' )
		) );
		
		if ( !is_wp_error( $response ) ) {
			update_option( 'custom_login_tracking_last_send', time() );
		}

	}

	/**
	 * Check for a new opt-in on settings save
	 *
	 * This runs during the sanitation of General settings, thus the return
	 *
	 * @access public
	 * @return array
	 */
	public function check_for_settings_optin( $input ) {
		
		// Send an intial check in on settings save
		if ( isset( $input['tracking'] ) && 'on' === $input['tracking'] ) {
			$this->send_checkin( true, array( 'on_activation' => 'settings', 'mailchimp_sub' => 'yes' ) );
		}

		return $input;
	}

	/**
	 * Check for a new opt-in via the admin notice
	 *
	 * @access public
	 * @return void
	 */
	public function check_for_optin( $data ) {
		
		$options = get_option( $this->option, array() );
		
		#var_dump( $options ); exit;
		
		$options['tracking'] = 'on';
		update_option( $this->option, $options );
		update_option( 'custom_login_hide_tracking_notice', '1' );
		
		$this->send_checkin( true, array( 'on_activation' => 'admin notice', 'mailchimp_sub' => 'yes' ) );
		
		wp_redirect( remove_query_arg( 'action' ) );
		exit;
	}

	/**
	 * Check for a new opt-in via the admin notice
	 *
	 * @access public
	 * @return void
	 */
	public function check_for_optout( $data ) {
		
		$options = get_option( $this->option, array() );
		
		#var_dump( $options ); exit;
		
		$options['tracking'] = 'off';
		update_option( $this->option, $options );
		update_option( 'custom_login_hide_tracking_notice', '1' );

		wp_redirect( remove_query_arg( 'action' ) );
		exit;
	}

	/**
	 * Get the last time a checkin was sent
	 *
	 * @access private
	 * @return false/string
	 */
	private function get_last_send() {
		return get_option( 'custom_login_tracking_last_send' );
	}

	/**
	 * Schedule a weekly checkin
	 *
	 * @access private
	 * @return void
	 */
	private function schedule_send() {
		// We send once a week (while tracking is allowed) to check in, which can be used to determine active sites
		add_action( 'custom_login_weekly_scheduled_events', array( $this, 'send_checkin' ) );
	}

	/**
	 * Display the admin notice to users that have not opted-in or out
	 *
	 * @access public
	 * @return void
	 */
	public function admin_notice() {

		$options		= get_option( $this->option, array() );
		$hide_notice	= get_option( 'custom_login_hide_tracking_notice' );

		if ( $hide_notice )
			return;

		if ( isset( $options['admin_notices'] ) && 'off' === $options['admin_notices'] )
			return;

		if ( isset( $options['tracking'] ) )
			return;

		if ( ! current_user_can( 'manage_options' ) )
			return;

		if ( 
			stristr( network_site_url( '/' ), 'dev'       ) !== false ||
			stristr( network_site_url( '/' ), 'localhost' ) !== false ||
			stristr( network_site_url( '/' ), ':8888'     ) !== false // This is common with MAMP on OS X
		) {
			update_option( 'custom_login_hide_tracking_notice', '1' ); // Don't update the notice in case someone pushes local to live? Maybe return.
		}
		else {
			$admin_url  = admin_url( 'admin.php' );
			$optin_url  = add_query_arg( 'action', 'cl_opt_into_tracking' );
			$optout_url = add_query_arg( 'action', 'cl_opt_out_of_tracking' );

			echo '<div class="updated"><p>';
				echo __( 'Allow Custom Login to anonymously track how this plugin is used and help us make the plugin better?<br>Opt-in and receive a 20% discount code for any plugin on our <a href="https://frosty.media/plugins" target="_blank">site</a>. No sensitive data is tracked.', CUSTOM_LOGIN_DIRNAME );
				echo '<span class="alignright">';
				echo '<a href="' . esc_url( $optin_url ) . '" class="">' . __( 'Allow', CUSTOM_LOGIN_DIRNAME ) . '</a> | ';
				echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="">' . __( 'Do not allow', CUSTOM_LOGIN_DIRNAME ) . '</a>';
				echo '</span>';
			echo '</p></div>';
		}
	}

}
$GLOBALS['cl_tracking'] = new CL_Tracking;