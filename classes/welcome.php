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
class Custom_Login_Welcome {
	
	/**
	 * @var string
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @access      private
	 * @since       2.0
	 * @return      void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Register dashboard pages.
	 *
	 * Later they are hidden in 'admin_head'.
	 *
	 * @since 2.0
	 * @return void
	 */
	public function admin_menus() {
		$login = CUSTOMLOGIN();
		// About Page
		add_dashboard_page(
			__( 'Welcome to Custom Login', $login->domain ),
			__( 'Welcome to Custom Login', $login->domain ),
			$this->minimum_capability,
			'custom-login-about',
			array( $this, 'about_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Menus
	 *
	 * @since       2.0
	 * @return      void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'custom-login-about' );

		// Badge for welcome page
		$badge_url = CUSTOM_LOGIN_URL . 'assets/images/welcome-badge.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.cl-badge {
			padding-top: 150px;
			height: 52px;
			width: 185px;
			color: #666;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
			margin: 0 -5px;
			background: url('<?php echo $badge_url; ?>') no-repeat;
		}

		.about-wrap .cl-badge {
			position: absolute;
			top: 0;
			right: 0;
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @since      2.0
	 */
	public function about_screen() {
		list( $display_version ) = explode( '-', CUSTOM_LOGIN_VERSION );
		$login = CUSTOMLOGIN();
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to Custom Login %s', $login->domain ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Custom Login %s is ready to make your login page better!', $login->domain ), $display_version ); ?></div>
			<div class="cl-badge"><?php printf( __( 'Version %s', $login->domain ), $display_version ); ?></div>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'custom-login-about' ), 'index.php' ) ) ); ?>">
					<?php _e( "What's New", $login->domain ); ?>
				</a>
			</h2>

			<div class="changelog">
				<h3><?php _e( 'Improved Settings', $login->domain ); ?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Load times', $login->domain ); ?></h4>
					<p><?php _e( 'There were issues in the past version that had the plugin loading on pages it wasn\'t supposted to. <strong>CL</strong> now caches all queries and database calls.', $login->domain ); ?></p>

					<h4><?php _e( 'Better features', $login->domain ); ?></h4>
					<p><?php printf( __( 'With a complete rewrite of the plugin, I have to ability to build custom add-ons (think extensions), like stealth login, email logins and 2-step authentication to name a few of the add-ons you can find on %sExtendd.com%s in the future.', $login->domain ), '<a href="http://extendd.com">', '</a>' ); ?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Under the Hood', $login->domain ); ?></h3>

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Settings Class', $login->domain ); ?></h4>
						<p><?php printf( __( 'The new settings class allows to add your own add-ons! Documentation can be found on the %sExtendd.com%s site.', $login->domain ), '<a href="http://extendd.com/documentation/">', '</a>' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Templates', $login->domain ); ?></h4>
						<p><?php printf( __( 'Custom login %stemplates%s can be created in your theme using the new login template add-on (coming soon).', $login->domain ), '<a href="http://extendd.com/documentation/">', '</a>' ); ?></p>
					</div>

					<div class="last-feature">
						
					</div>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . $login->domain ) ); ?>"><?php _e( 'Go to Custom Login Settings', $login->domain ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Sends user to the welcome page on first activation
	 *
	 * @since      2.0
	 * @return     void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_custom_login_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_custom_login_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		wp_safe_redirect( admin_url( 'index.php?page=custom-login-about' ) ); exit;

	}
}
new Custom_Login_Welcome();