<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Settings_Upgrade
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class CL_Settings_Upgrade {

	/** Singleton *************************************************************/
	private static $instance;

	protected $parent;

	/**
	 * Main Instance
	 *
	 * @staticvar 	array 	$instance
	 * @return 		The one true instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->actions();
			self::$instance->parent = CUSTOMLOGIN();
		}
		return self::$instance;
	}

	private function actions() {

		add_action( 'admin_notices',								array( $this, 'upgrade_notices' ) );
		add_action( 'admin_menu',								array( $this, 'add_submenu_page' ) );
		add_action( 'wp_ajax_custom_login_trigger_upgrades',		array( $this, 'trigger_upgrades' ) );
	}

	/**
	 * Display Upgrade Notices
	 *
	 * @access      private
	 * @since       2.0
	 * @return      void
	 */
	public function upgrade_notices() {

		if ( isset( $_GET['page'] ) && $_GET['page'] == ( 'custom-login-upgrades' || 'custom-login' ) )
			return; // Don't show notices on the upgrades page

		$cl_version = get_option( CUSTOM_LOGIN_OPTION . '_version' );

		if ( ! $cl_version ) {
			// 2.0 is the first version to use this option so we must add it
			$cl_version = '2.0';
		}

		$cl_version = preg_replace( '/[^0-9.].*/', '', $cl_version );

		// Version less than 2.0 (settings exist)
		if ( false !== ( $old_settings = get_option( 'custom_login_settings', false ) ) ) {

			// New install
			if ( !$old_settings )
				return;

			if ( !empty( $old_settings ) && !empty( $old_settings['version'] ) )
				$cl_version = $old_settings['version'];

			// Versions less than 2.0
			if ( version_compare( $cl_version, '2.0', '<' ) ) {
				printf(
					'<div class="updated"><p>' . __( 'Custom Login needs to upgrade the settings, please click <a href="%s">here</a> to start the upgrade.', CUSTOM_LOGIN_DIRNAME ) . '</p></div>',
					esc_url( add_query_arg( array( 'ver' => '2.0' ), admin_url( 'options.php?page=custom-login-upgrades' ) ) )
				);
			}
		} // 2.0

		// Version less than 3.0 (settings exist)
		if ( false !== ( $old_settings = get_option( 'custom_login', false ) ) ) {

			// Versions less than 3.0
			if ( version_compare( $cl_version, '3.0', '<' ) ) {
				printf(
					'<div class="updated"><p>' . __( 'Custom Login needs to upgrade the settings database, please click <a href="%s">here</a> to start the upgrade.', CUSTOM_LOGIN_DIRNAME ) . '</p></div>',
					add_query_arg( array( 'ver' => '3.0' ), admin_url( 'options.php?page=custom-login-upgrades' ) )
				);
			}
		} // 3.0

		// Version less than 3.1
		if ( version_compare( CUSTOM_LOGIN_VERSION, '3.1', '<' ) ) {
			printf(
				'<div class="notice is-dismissible"><p>' . __( 'Custom Login needs to upgrade the database, please click <a href="%s">here</a> to start the upgrade.', CUSTOM_LOGIN_DIRNAME ) . '</p></div>',
				esc_url( add_query_arg( array( 'ver' => '3.1' ), admin_url( 'options.php?page=custom-login-upgrades' ) ) )
			);
		} // 3.1
	}

	/**
	 * Add Submenu Upgrade page
	 *
	 * @access      private
	 * @since       1.0
	 * @return      void
	 */
	public function add_submenu_page() {

		add_submenu_page(
			null,
			__( 'Custom Login Upgrades', CUSTOM_LOGIN_DIRNAME ),
			__( 'Custom Login Upgrades', CUSTOM_LOGIN_DIRNAME ),
			'update_plugins',
			'custom-login-upgrades',
			array( $this, 'upgrades_screen' )
		);
	}

	/**
	 * Render Upgrades Screen
	 *
	 * @access      private
	 * @since       2.0
	 * @return      void
	*/
	function upgrades_screen() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Custom Login - Upgrades', CUSTOM_LOGIN_DIRNAME ); ?></h2>
			<div id="custom-login-upgrade-status">
				<p>
					<?php _e( 'The upgrade process has started, please be patient. This could take several minutes. You will be automatically redirected when the upgrade is finished.', CUSTOM_LOGIN_DIRNAME ); ?>
					<img src="<?php echo esc_url( admin_url( 'images/loading.gif' ) ); ?>" id="custom-login-upgrade-loader"/>
				</p>
			</div>
			<script type="text/javascript">
				jQuery( document ).ready( function($) {
					// Trigger upgrades on page load
					var data = {
						action	: 'custom_login_trigger_upgrades',
						nonce	: '<?php echo wp_create_nonce( 'CL_Settings_Upgrade' . basename( __FILE__ ) ); ?>'
					};
					$.post( ajaxurl, data, function (response) {
						if ( response == 'complete' ) {
							$('#custom-login-upgrade-loader').hide();
							document.location.href = 'options-general.php?page=custom-login';
						}
					});
				});
			</script>
		</div>
		<?php
	}

	/**
	 * Triggers all upgrade functions
	 *
	 * This function is usually triggered via ajax
	 *
	 * @access      private
	 * @since       2.0
	*/
	public function trigger_upgrades() {

		check_ajax_referer( 'CL_Settings_Upgrade' . basename( __FILE__ ), 'nonce' );

		$cl_version = get_option( CUSTOM_LOGIN_OPTION . '_version' );

		if ( ! $cl_version ) {
			// 2.0 is the first version to use this option so we must add it
			$cl_version = '2.0';
			add_option( CUSTOM_LOGIN_OPTION . '_version', $cl_version );
		}

		// Version less than 2.0
		if ( false !== ( $old_settings = get_option( 'custom_login_settings', false ) ) ) {

			$cl_version = '1.0';

			if ( !empty( $old_settings ) && !empty( $old_settings['version'] ) )
				$cl_version = $old_settings['version'];

			if ( version_compare( $cl_version, '2.0', '<' ) ) {
				$this->cl_v20_upgrades();
			}

		} // 2.0

		// Version less than 3.0
		if ( false !== ( $old_settings = get_option( 'custom_login', false ) ) ) {

			$cl_version = '2.0';

			if ( !empty( $old_settings ) && !empty( $old_settings['version'] ) )
				$cl_version = $old_settings['version'];

			if ( version_compare( $cl_version, '3.0', '<' ) ) {
				$this->cl_v30_upgrades();
			}

		} // 3.0

		// Version less than 3.1
		if ( version_compare( $cl_version, '3.1', '<' ) ) {
			$this->cl_v31_upgrades();
		} // 3.1

		update_option( CUSTOM_LOGIN_OPTION . '_version', CUSTOM_LOGIN_VERSION );

		if ( DOING_AJAX ) {
			die( 'complete' );
		}
	}

	/**
	 * Upgrade routine for v2.0
	 *
	 * @access      private
	 * @since       2.0.0
	 * @return      void
	 */
	private function cl_v20_upgrades() {

		$old_settings = get_option( 'custom_login_settings' );
		$new_settings = get_option( 'custom_login', array() );

		$new_settings['version'] = $this->parent->version;
		$new_settings['active'] = true === $old_settings['custom'] ? 'on' : 'off';
		$new_settings['html_background_color'] = CL_Scripts_Styles::is_rgba( $old_settings['html_background_color'] ) ? CL_Scripts_Styles::rgba2hex( $old_settings['html_background_color'] ) : $old_settings['html_background_color'];
		$new_settings['html_background_color_checkbox'] = 'off';
		$new_settings['html_background_color_opacity'] = '';
		$new_settings['html_background_url'] = $old_settings['html_background_url'];
		$new_settings['html_background_position'] = 'left top';
		$new_settings['html_background_repeat'] = $old_settings['html_background_repeat'];
		$new_settings['html_background_size'] = $old_settings['html_background_size'];
		$new_settomgs['hide_wp_logo'] = 'on';
		$new_settings['logo_background_url'] = $old_settings['login_form_logo'];
		$new_settings['logo_background_position'] = 'top center';
		$new_settings['logo_background_repeat'] = '';
		$new_settings['logo_background_size'] = '';
		$new_settings['login_form_background_color'] = CL_Scripts_Styles::is_rgba( $old_settings['html_background_color'] ) ? CL_Scripts_Styles::rgba2hex( $old_settings['login_form_background_color'] ) : $old_settings['login_form_background_color'];
		$new_settings['login_form_background_color_checkbox'] = 'off';
		$new_settings['login_form_background_color_opacity'] = '';
		$new_settings['login_form_background_url'] = $old_settings['login_form_background'];
		$new_settings['login_form_background_position'] = '';
		$new_settings['login_form_background_repeat'] = '';
		$new_settings['login_form_background_size'] = $old_settings['login_form_background_size'];
		$new_settings['login_form_border_radius'] = $old_settings['login_form_border_radius'];
		$new_settings['login_form_border_size'] = $old_settings['login_form_border'];
		$new_settings['login_form_border_color'] = CL_Scripts_Styles::is_rgba( $old_settings['html_background_color'] ) ? CL_Scripts_Styles::rgba2hex( $old_settings['login_form_border_color'] ) : $old_settings['login_form_border_color'];
		$new_settings['login_form_border_color_checkbox'] = 'off';
		$new_settings['login_form_border_color_opacity'] = '';
		$new_settings['login_form_box_shadow'] = $old_settings['login_form_box_shadow_1'] . 'px ' . $old_settings['login_form_box_shadow_2'] . 'px ' . $old_settings['login_form_box_shadow_3'] . 'px';
		$new_settings['login_form_box_shadow_color'] = CL_Scripts_Styles::is_rgba( $old_settings['html_background_color'] ) ? CL_Scripts_Styles::rgba2hex( $old_settings['login_form_box_shadow_4'] ) : $old_settings['login_form_box_shadow_4'];
		$new_settings['login_form_box_shadow_color_checkbox'] = 'off';
		$new_settings['login_form_box_shadow_color_opacity'] = '';
		$new_settings['label_color'] = CL_Scripts_Styles::is_rgba( $old_settings['html_background_color'] ) ? CL_Scripts_Styles::rgba2hex( $old_settings['label_color'] ) : $old_settings['label_color'];
		$new_settings['label_color_checkbox'] = 'off';
		$new_settings['label_color_opacity'] = '';
		$new_settings['nav_color'] = '';
		$new_settings['nav_color_checkbox'] = 'off';
		$new_settings['nav_color_opacity'] = '';
		$new_settings['nav_text_shadow_color'] = '';
		$new_settings['nav_text_shadow_color_checkbox'] = 'off';
		$new_settings['nav_text_shadow_color_opacity'] = '';
		$new_settings['nav_hover_color'] = '';
		$new_settings['nav_hover_color_checkbox'] = 'off';
		$new_settings['nav_hover_color_opacity'] = '';
		$new_settings['nav_text_shadow_hover_color'] = '';
		$new_settings['nav_text_shadow_hover_color_checkbox'] = 'off';
		$new_settings['nav_text_shadow_hover_color_opacity'] = '';
		$new_settings['custom_css'] = wp_filter_nohtml_kses( $old_settings['custom_css'] );
		$new_settings['custom_html'] = wp_kses_post( $old_settings['custom_html'] );
		$new_settings['custom_jquery'] = wp_specialchars_decode( stripslashes( $old_settings['custom_jquery'] ), 1, 0, 1 );

		update_option( 'custom_login', $new_settings );
		delete_option( 'custom_login_settings' );
		return true;
	}

	/**
	 * Upgrade routine for v3.0
	 *
	 * @access      private
	 * @since       3.0.0
	 * @return      void
	 */
	private function cl_v30_upgrades() {

		$old_settings		= get_option( 'custom_login', array() );
		$design_settings	= get_option( CUSTOM_LOGIN_OPTION . '_design', array() );
		$general_settings	= get_option( CUSTOM_LOGIN_OPTION . '_general', array() );

		/** Design */
		$design_settings['html_background_color'] = $this->get_old_setting( $old_settings, 'html_background_color' );
		$design_settings['html_background_color_checkbox'] = $this->get_old_setting( $old_settings, 'html_background_color_checkbox' );
		$design_settings['html_background_color_opacity'] = $this->get_old_setting( $old_settings, 'html_background_color_opacity' );
		$design_settings['html_background_url'] = $this->get_old_setting( $old_settings, 'html_background_url' );
		$design_settings['html_background_position'] = $this->get_old_setting( $old_settings, 'html_background_position' );
		$design_settings['html_background_repeat'] = $this->get_old_setting( $old_settings, 'html_background_repeat' );
		$design_settings['html_background_size'] = $this->get_old_setting( $old_settings, 'html_background_size' );

		$design_settings['logo_force_form_max_width'] = 'off'; // New
		$design_settings['hide_wp_logo'] = $this->get_old_setting( $old_settings, 'hide_wp_logo' );
		$design_settings['logo_background_url'] = $this->get_old_setting( $old_settings, 'logo_background_url' );
		$design_settings['logo_background_size_width'] = $this->get_old_setting( $old_settings, 'logo_background_size_width' );
		$design_settings['logo_background_size_height'] = $this->get_old_setting( $old_settings, 'logo_background_size_height' );
		$design_settings['logo_background_position'] = $this->get_old_setting( $old_settings, 'logo_background_position' );
		$design_settings['logo_background_repeat'] = $this->get_old_setting( $old_settings, 'logo_background_repeat' );
		$design_settings['logo_background_size'] = $this->get_old_setting( $old_settings, 'logo_background_size' );

		$design_settings['login_form_width'] = ''; // New

		$design_settings['login_form_background_color'] = $this->get_old_setting( $old_settings, 'login_form_background_color' );
		$design_settings['login_form_background_color_checkbox'] = $this->get_old_setting( $old_settings, 'login_form_background_color_checkbox' );
		$design_settings['login_form_background_color_opacity'] = $this->get_old_setting( $old_settings, 'login_form_background_color_opacity' );
		$design_settings['login_form_background_url'] = $this->get_old_setting( $old_settings, 'login_form_background_url' );
		$design_settings['login_form_background_position'] = $this->get_old_setting( $old_settings, 'login_form_background_position' );
		$design_settings['login_form_background_repeat'] = $this->get_old_setting( $old_settings, 'login_form_background_repeat' );
		$design_settings['login_form_background_size'] = $this->get_old_setting( $old_settings, 'login_form_background_size' );

		$design_settings['login_form_border_radius'] = $this->get_old_setting( $old_settings, 'login_form_border_radius' );
		$design_settings['login_form_border_size'] = $this->get_old_setting( $old_settings, 'login_form_border_size' );
		$design_settings['login_form_border_color'] = $this->get_old_setting( $old_settings, 'login_form_border_color' );
		$design_settings['login_form_border_color_checkbox'] = $this->get_old_setting( $old_settings, 'login_form_border_color_checkbox' );
		$design_settings['login_form_border_color_opacity'] = $this->get_old_setting( $old_settings, 'login_form_border_color_opacity' );
		$design_settings['login_form_box_shadow'] = $this->get_old_setting( $old_settings, 'login_form_box_shadow' );
		$design_settings['login_form_box_shadow_color'] = $this->get_old_setting( $old_settings, 'login_form_box_shadow_color' );
		$design_settings['login_form_box_shadow_color_checkbox'] = $this->get_old_setting( $old_settings, 'login_form_box_shadow_color_checkbox' );
		$design_settings['login_form_box_shadow_color_opacity'] = $this->get_old_setting( $old_settings, 'login_form_box_shadow_color_opacity' );

		$design_settings['label_color'] = $this->get_old_setting( $old_settings, 'label_color' );
		$design_settings['label_color_checkbox'] = $this->get_old_setting( $old_settings, 'label_color_checkbox' );
		$design_settings['label_color_opacity'] = $this->get_old_setting( $old_settings, 'label_color_opacity' );

		$design_settings['nav_color'] = $this->get_old_setting( $old_settings, 'nav_color' );
		$design_settings['nav_color_checkbox'] = $this->get_old_setting( $old_settings, 'nav_color_checkbox' );
		$design_settings['nav_color_opacity'] = $this->get_old_setting( $old_settings, 'nav_color_opacity' );
		$design_settings['nav_text_shadow_color'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_color' );
		$design_settings['nav_text_shadow_color_checkbox'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_color_checkbox' );
		$design_settings['nav_text_shadow_color_opacity'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_color_opacity' );
		$design_settings['nav_hover_color'] = $this->get_old_setting( $old_settings, 'nav_hover_color' );
		$design_settings['nav_hover_color_checkbox'] = $this->get_old_setting( $old_settings, 'nav_hover_color_checkbox' );
		$design_settings['nav_hover_color_opacity'] = $this->get_old_setting( $old_settings, 'nav_hover_color_opacity' );
		$design_settings['nav_text_shadow_hover_color'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_hover_color' );
		$design_settings['nav_text_shadow_hover_color_checkbox'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_hover_color_checkbox' );
		$design_settings['nav_text_shadow_hover_color_opacity'] = $this->get_old_setting( $old_settings, 'nav_text_shadow_hover_color_opacity' );

		$design_settings['custom_css'] = wp_filter_nohtml_kses( $this->get_old_setting( $old_settings, 'custom_css' ) );
		$design_settings['animate.css'] = 'off'; // New
		$design_settings['custom_html'] = wp_kses_post( $this->get_old_setting( $old_settings, 'custom_html' ) );
		$design_settings['custom_jquery'] = wp_specialchars_decode( stripslashes( $this->get_old_setting( $old_settings, 'custom_jquery' ) ), 1, 0, 1 );

		/** General */
		$general_settings['active'] = $this->get_old_setting( $old_settings, 'active', 'on' );
		$general_settings['capability'] = 'manage_options'; // New
		$general_settings['tracking'] = 'off'; // New
		$general_settings['admin_notices'] = 'off'; // New
		$general_settings['wp_shake_js'] = 'off'; // New
		$general_settings['remove_login_css'] = 'off'; // New
		$general_settings['lostpassword_text'] = 'off'; // New

		update_option( CUSTOM_LOGIN_OPTION . '_design', $design_settings );
		update_option( CUSTOM_LOGIN_OPTION . '_general', $general_settings );
		delete_option( 'custom_login' );
		return true;
	}

	/**
	 * Upgrade routine for v3.1
	 *
	 * @access      private
	 * @since       3.1
	 * @return      void
	 */
	private function cl_v31_upgrades() {

		$general_settings = get_option( CUSTOM_LOGIN_OPTION . '_general', array() );

		// Remove old settings
		unset( $general_settings['allow_password_reset'] );
		unset( $general_settings['auth_timeout'] );
		unset( $general_settings['auth_timeout_remember'] );

		// Leave
		$general_settings['active'] = $this->get_old_setting( $general_settings, 'active', 'on' );

		// New settings
		$general_settings['dashboard_widget'] = 'off';

		update_option( CUSTOM_LOGIN_OPTION . '_general', $general_settings );

		// Update tracking options name
		update_option( CUSTOM_LOGIN_OPTION . '_tracking_last_send', get_option( 'cl_tracking_last_send' ) );
		update_option( CUSTOM_LOGIN_OPTION . '_tracking_notice', get_option( 'cl_tracking_notice' ) );
		delete_option( 'cl_tracking_last_send' );
		delete_option( 'cl_tracking_notice' );

		delete_option( CUSTOM_LOGIN_OPTION . '_announcement_message' );

		/** Cleanup Cron Events */
		wp_clear_scheduled_hook( 'cl_daily_scheduled_events' );
		wp_clear_scheduled_hook( 'cl_weekly_scheduled_events' );
		return true;
	}

	/**
	 * Helper function to check if option isset
	 *
	 * @since	12/26/2014
	 */
	private function get_old_setting( $setting = array(), $option = null, $default = '' ) {
		if ( is_null( $option ) )
			return $default;

		if ( isset( $setting[$option] ) )
			return $setting[$option];

		return $default;
	}

}
CL_Settings_Upgrade::instance();
