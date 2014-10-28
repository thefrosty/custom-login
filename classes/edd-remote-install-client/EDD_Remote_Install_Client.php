<?php

/**
 * Allows plugins to install new plugins or upgrades
 *
 * @author Mindshare Studios, Inc.
 * @version 1.7.2 (FROSTY HACK)
 */

class Extendd_Remote_Install_Client {
	private $api_url  = '';
	private $options = array(
			'skipplugincheck'	=> false
		);

	/**
	 * Class constructor.
	 *
	 *
	 * @param string $_api_url The URL pointing to the custom API endpoint.
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array $_api_data Optional data to send with API calls.
	 * @return void
	 */
	function __construct( $_api_url, $page, $options = array() ) {
		$this->api_url  = trailingslashit( $_api_url );

		if(isset($options['skipplugincheck']) && $options['skipplugincheck'] == true) {
			$this->options['skipplugincheck'] = true;
		}

		$options['page'] = $page;
		$this->options = $options;

		add_action( 'load-' . $page, array($this, 'register_scripts' ));

		add_action('wp_ajax_edd-activate-plugin-' . $page, array($this, 'activate_plugin'));
		add_action('wp_ajax_edd-deactivate-plugin-' . $page, array($this, 'deactivate_plugin'));
		add_action('wp_ajax_edd-check-plugin-status-' . $page, array($this, 'check_plugin_status'));
		add_action('wp_ajax_edd-check-remote-install-' . $page, array($this, 'check_remote_install'));
		add_action('wp_ajax_edd-do-remote-install-' . $page, array($this, 'do_remote_install'));

		add_action('wp_ajax_edd-do-manual-install-' . $page, array($this, 'do_manual_install'));
		add_action( 'plugins_api', array($this, 'plugins_api'), 100, 3 );

		add_action('eddri-install-complete-' . $page, array($this, 'install_complete'), 0, 1);
	}

	/**
	 * Try to convert plugin name to slug
	 *
	 * @param $str Download name
	 * @return $str Slug
	 */

	private function slug($str) {
		$str = strtolower( $str );
		$str = preg_replace("/[\s_]/", "-", $str);

		return $str;
	}

	/**
	 * Register scripts and styles
	 *
	 * @return void
	 */

	public function register_scripts() {
		wp_enqueue_script('edd-remote-install-script', plugin_dir_url( __FILE__ ) . '/js/edd-remote-install-admin.js', array('jquery'));
		wp_enqueue_style('edd-remote-install-style', plugin_dir_url( __FILE__ ) . '/css/edd-remote-install-admin.css');

		wp_localize_script( 'edd-remote-install-script', 'edd_ri_options', $this->options );
	}

	/**
	 * Do manual install
	 *
	 * If a plugin was unable to be installed automatically, generate an install URL and redirect to the plugins API
	 *
	 * @param string $_POST['download'] Download requested
	 * @param string $_POST['license'] License key
	 * @return string $url
	 */

	public function do_manual_install() {

		$download_name = urlencode($_POST['download']);
		$download_slug = $this->slug($_POST['download']);

		$license = '';
		if(isset($_POST['license']))
			$license = $_POST['license'];

		$nonce = wp_create_nonce('install-plugin_' . $download_slug);

		$url = admin_url('update.php?action=install-plugin&plugin=' . $download_slug . '&name=' . $download_name . '&license=' . $license . '&_wpnonce=' . $nonce . '&eddri=' . $this->options['page']);

		die($url);

	}

	/**
	 * Plugins API
	 *
	 * Overrides the plugins API parameters for download URLs originated by EDDRI
	 *
	 * @param string $_GET['eddri'] EDDRI page that originated the request
	 * @param string $_GET['license'] License key
	 * @param string $_GET['name'] Name of the plugin requested
	 * @return obj $api
	 */

	public function plugins_api($api, $action, $args) {

		if($action = 'plugin_information') {

			if(isset($_GET['eddri']) && $_GET['eddri'] == $this->options['page']) {

				$api_params = array(
					'edd_action' => 'get_download',
					'item_name'  => urlencode( $_GET['name'] ),
					'license'	 => urlencode( $_GET['license'] )
				);

				$download_link = add_query_arg($api_params, $this->api_url);
				
				///////////// NEW /////////////////
				$download_id = $this->get_remote_download_id( $_GET['name'] );				
				$download_link = $this->get_encoded_download_package_url( $download_id, $_GET['license'] );
				///////////// NEW /////////////////

			    $api = new stdClass();
		        $api->name = $args->slug;
		        $api->version = "";
		        $api->download_link = $download_link;

			}

	    }

	    return $api;

	}

	/**
	 * Callback action that's fired when an install is completed successfully
	 *
	 * @param array $args Install complete arguments
	 * @return void
	 */

	public function install_complete($args) {


	}

	/**
	 * Check plugin status
	 *
	 * Checks to see if a plugin is currently installed and disables the install button if so
	 *
	 * @param string $_POST['download'] Download requested
	 * @return string $response
	 */

	public function check_plugin_status() {
		
		$plugin = $this->slug($_POST['download']);

		if (is_plugin_active($plugin . '/' . $plugin . '.php')) {
			die("active");
		} elseif (file_exists(WP_PLUGIN_DIR . '/' . $plugin . '/' . $plugin . '.php')) {
			die("installed");
		} {
			die(false);
		}
	}

	/**
	 * Check remote install
	 *
	 * Checks remote server for the specified Download
	 *
	 * @param string $_POST['download'] Download requested
	 * @return string $response
	 */

	public function check_remote_install() {

		if ( ! current_user_can('install_plugins') )
			die( 'You do not have sufficient permissions to install plugins on this site.' );

		$api_params = array(
			'edd_action' => 'check_download',
			'item_name'  => urlencode( $_POST['download'] )
		);

		$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( ! is_wp_error( $request ) ):
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			$request = maybe_unserialize( $request );

			if($request->download == "free") {
				
				$response = "0";

			} else if ($request->download == "not-free") {

				$response = "1";

			} else {

				$response = "does not exist";

			}

		else:

			$response = "Error occurred while trying to reach remote server. Please try again or contact support.";

		endif;

		die(json_encode($response));
	}

	/**
	 * Activate plugin
	 *
	 * Attemps to activate a plugin which is installed and inactive. Triggered by user clicking "Activate".
	 *
	 * @param string $_POST['download'] Download requested
	 * @return response
	 */

	public function activate_plugin() {

		$slug = $this->slug($_POST['download']);
		$path = WP_PLUGIN_DIR . "/" . $slug . "/" . $slug . ".php";
		activate_plugin( $path );

		if(is_plugin_active( $slug . '/' . $slug . '.php' )) {
			die('activated');
		} else {
			die('error');
		}

	}

	/**
	 * Deactivate plugin
	 *
	 * Attemps to deactivate a plugin. Triggered by user clicking "Deactivate".
	 *
	 * @param string $_POST['download'] Download requested
	 * @return response
	 */

	public function deactivate_plugin() {

		$slug = $this->slug($_POST['download']);
		$path = WP_PLUGIN_DIR . "/" . $slug . "/" . $slug . ".php";
		deactivate_plugins( $path );

		if(!is_plugin_active( $slug . '/' . $slug . '.php' )) {
			die('deactivated');
		} else {
			die('error');
		}

	}

	/**
	 * Manual install
	 *
	 * Outputs full install log in cases where auto-install failed
	 *
	 * @param string $_POST['download'] Download requested
	 * @return response
	 */

	public function manual_install() {

		echo "Hi";

	}

	/**
	 * Do remote install
	 *
	 * Passes the download and license key (if specified) to the server and receives and installs the plugin package
	 *
	 * @param string $_POST['license'] License key (if specified)
	 * @param string $_POST['download'] Download requested
	 * @return response
	 */

	public function do_remote_install() {

		if ( ! current_user_can('install_plugins') )
			wp_die( 'You do not have sufficient permissions to install plugins on this site.' );

		$download = $_POST['download'];

		if(isset($_POST['license'])) {
			$license = $_POST['license'];

			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $download ) // the name of our product in EDD
			);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if($license_data->license != "valid") 
				die("invalid");

		} else {

			// If its a free download, don't send a license
			$license = null;

		}

		$api_params = array(
			'edd_action' => 'get_download',
			'item_name'  => urlencode( $download ),
			'license'	 => urlencode( $license )
		);
		
		//$download_link = add_query_arg($api_params, $this->api_url);
		
		// decode the license data
		$download_id = $this->get_remote_download_id( $download );
		
		$download_link = $this->get_encoded_download_package_url( $download_id, $license );
		//var_dump( $download_link ); exit;

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; //for plugins_api..

		$upgrader = new Plugin_Upgrader();

		$result = $upgrader->install($download_link);

		if($result == 1) {
			$slug = $this->slug($download);
			$path = WP_PLUGIN_DIR . "/" . $slug . "/" . $slug . ".php";
			$result = activate_plugin( $path );

			$args['slug'] = $slug;
			$args['license'] = $license;
			do_action('eddri-install-complete-' . $this->options['page'], $args);
		}

		die();
	}
	
	private function get_remote_download_id( $download_title, $download_id = null ) {

		$api_params = array(
			'edd_action' => 'get_download_id',
			'item_name'  => urlencode( $download_title )
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( !is_wp_error( $response ) )
			$download_id = json_decode( wp_remote_retrieve_body( $response ) );
			
		return $download_id;
	}
	
	private function get_encoded_download_package_url( $download_id, $license ) {

		$package_url = add_query_arg( array(
			'edd_action' 	=> 'package_download',
			'id' 			=> $download_id,
			'key' 			=> $license,
			'expires'		=> rawurlencode( base64_encode( strtotime( '+1 hour' ) ) )
		 ), $this->api_url );

		return apply_filters( 'edd_sl_encoded_package_url', $package_url );

	}
}