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
 * @since  3.1
 * @return void
 */
class CL_Import_Export {

	/** Singleton *************************************************************/
	private static $instance;

	/**
	 * The menu
	 *
	 * @access private
	 */
	private $settings_api;
	private $settings_id;

	/**
	 * Main Instance
	 *
	 * @staticvar 	array 	$instance
	 * @return 		The one true instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Get things going
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		
		add_action( 'admin_init',												array( $this, 'admin_init' ) );
		add_action( CUSTOM_LOGIN_OPTION . '_settings_sidebars',					array( $this, 'settings_sidebar' ), 30 );
		add_action( CUSTOM_LOGIN_OPTION . '_after_settings_sections_form',		array( $this, 'after_settings_sections_form' ), 11 );
		add_action( 'admin_action_' . CUSTOM_LOGIN_OPTION . '_download_export',	array( $this, 'download_export' ) );
	}
	
	/**
	 * Set our settings fields
	 *
	 * @access private
	 */
	private function settings_fields() {
		
		$fields	[ $this->settings_id ] = array(
			array(
				'name' 		=> 'import',
				'label' 		=> __( 'Import', CUSTOM_LOGIN_DIRNAME ),
				'desc' 		=> '',
				'type' 		=> 'textarea',
				'sanitize'	=> '__return_empty_string',
			),
			
			array(
				'name' 		=> 'export',
				'label'		=> __( 'Export', CUSTOM_LOGIN_DIRNAME ),
				'desc' 		=> sprintf( __( 'This textarea is always pre-filled with the current settings. Copy these settings for import at a later time, or <a href="%s">download</a> them.', CUSTOM_LOGIN_DIRNAME ),
					esc_url( wp_nonce_url(
						add_query_arg( array( 'action' => CUSTOM_LOGIN_OPTION . '_download_export' ),
							''
						),
						'export',
						'cl_nonce'
					) )
				),
				'default'	=> $this->get_custom_login_settings(),
				'type' 		=> 'textarea',
				'extra'		=> array(
				'readonly'	=> 'readonly'
				),
				'sanitize' => '__return_empty_string',
			),

		);
		
		return $fields;
	}
	
	/**
	 * Return the full array of settings
	 *
	 * @access private
	 */
	private function get_custom_login_settings() {
		
		$settings = array();
		include( trailingslashit( CUSTOM_LOGIN_DIR ) . 'includes/default-settings.php' );
		
		foreach ( $sections as $section ) {
			$settings[ $section['id'] ] = get_option( $section['id'] );
		}
		
		return base64_encode( maybe_serialize( $settings ) );
		
	#	var_dump( $settings ); exit;
	}
	
	public function admin_init() {
		
		$this->settings_api	= CUSTOMLOGIN()->settings_api;
		$this->settings_id	= CUSTOM_LOGIN_OPTION . '_import_export';
		
		add_settings_section( $this->settings_id, __( 'Import/Export Custom Login Settings', CUSTOM_LOGIN_DIRNAME ), '__return_false', $this->settings_id );
		
		foreach( $this->settings_fields() as $section => $field ) {
			foreach ( $field as $option ) {

				$type = isset( $option['type'] ) ? $option['type'] : 'text';
				
				$args = array(
					'id'			=> $option['name'],
					'desc' 			=> isset( $option['desc'] ) ? $option['desc'] : '',
					'name' 			=> $option['label'],
					'section' 		=> $section,
					'size' 			=> isset( $option['size'] ) ? $option['size'] : null,
					'options' 		=> isset( $option['options'] ) ? $option['options'] : '',
					'default'		=> isset( $option['default'] ) ? $option['default'] : '',
					'sanitize'		=> isset( $option['sanitize'] ) ? $option['sanitize'] : '',
				);
				$args = wp_parse_args( $args, $option );
				
				add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], array( $this->settings_api, 'callback_' . $type ), $section, $section, $args );
			}
		}
				
		register_setting( $this->settings_id, $this->settings_id, array( $this, 'sanitize_options' ) );
	}
	
	/**
	 * Box with a link to the extensions page.
	 */
	function settings_sidebar( $args ) {
		
		$html  = '<ul class="cl-sections-menu">';
		$html .= sprintf( '<li><a href="#%1$s">%2$s</a></li>', $this->settings_id, __( 'Import/Export Settings' ) );
		$html .= '</ul>';
		
		echo $html;
	}

    /**
     * Show the import/export settings form.
     */
    function after_settings_sections_form() {
		?>
		<div id="<?php echo $this->settings_id; ?>" class="group">
		<form action="options.php" id="<?php echo $this->settings_id; ?>form" method="post" >
			<?php settings_fields( $this->settings_id ); ?>
			<?php do_settings_sections( $this->settings_id ); ?>
			<?php submit_button(); ?>
		</form>
		</div><?php
    }

	/**
	 * Sanitize callback for Settings API before input into database.
	 *
	 * @ref		http://stackoverflow.com/a/10797086/558561
	 */ 
    public function maybe_import_settings( $options ) {
		
		if ( !empty( $options['import'] ) && ( base64_encode( base64_decode( $options['import'], true ) ) === $options['import'] ) ) {
			$import = maybe_unserialize( base64_decode( $options['import'] ) );
		#	var_dump( $import ); exit;
			if ( is_array( $import ) ) {
				foreach( $import as $setting_key => $settings ) {
					if ( false !== $settings ) {
						if ( update_option( $setting_key, $settings ) ) {
							add_settings_error(
								$this->settings_id	,
								esc_attr( 'settings_updated' ),
								__( 'Custom Login settings successfully imported', CUSTOM_LOGIN_DIRNAME ),
								'updated'
							);
						}
					}
				}
			}
		}
    }
	
    /**
     * Sanitize callback for Settings API
     */ 
    function sanitize_options( $options ) {
		
		$this->maybe_import_settings( $options );
		
		foreach( $options as $option_slug => $option_value ) {
			$sanitize_callback = $this->get_sanitize_callback( $option_slug );
		
			// If callback is set, call it
			if ( $sanitize_callback ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}
		
			// Treat everything that's not an array as a string
			if ( !is_array( $option_value ) ) {
				$options[ $option_slug ] = sanitize_text_field( $option_value );
				continue;
			}
		}
		
		$options = $this->after_sanitize_options( $options );
		
		return $options;
    }
        
    /**
     * Get sanitization callback for given option slug
     * 
     * @param string $slug option slug
     * 
     * @return mixed string or bool false
     */ 
    function get_sanitize_callback( $slug = '' ) {
        
		if ( empty( $slug ) )
			return false;
			
		// Iterate over registered fields and see if we can find proper callback
		foreach( $this->settings_fields() as $section => $options ) {
			foreach ( $options as $option ) {
				if ( $option['name'] != $slug )
					continue;
				// Return the callback name 
				return isset( $option['sanitize'] ) && is_callable( $option['sanitize'] ) ? $option['sanitize'] : false;
			}
		}
		return false; 
    }
	
    function after_sanitize_options( $options ) {
		
		foreach( $this->settings_fields() as $section => $field ) {
			foreach ( $field as $option ) {
				unset( $options[ $option['name'] ] );
			}
		}
		
		return $options;
	}
	
	/**
	 * Export the settings.
	 *
	 * @ref		http://stackoverflow.com/a/16440501/558561
	 */ 
	function download_export() {
		
		if ( !isset( $_GET['cl_nonce']) || !wp_verify_nonce( $_GET['cl_nonce'], 'export' ) ) {
			wp_redirect( remove_query_arg( array( 'action', 'cl_nonce' ) ) );
			exit;
		}

		$month = date( 'n' );
		$year  = date( 'Y' );
		
		ignore_user_abort(true);
		
		nocache_headers();
		header( 'Content-type: text/plain; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=cl-export-' . $month . '-' . $year . '.txt' );
		header( 'Expires: 0' );
		
		echo $this->get_custom_login_settings();
		exit;		
	}

}
$GLOBALS['cl_import_export'] = CL_Import_Export::instance();