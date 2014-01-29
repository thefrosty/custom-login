<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Extendd Plugin Settings API wrapper class
 *
 * @original author:	Tareq Hasan <tareq@weDevs.com>
 */
if ( !class_exists( 'Extendd_Plugin_Settings_API' ) ):
    class Extendd_Plugin_Settings_API {
		
	/**
	 * Version
	 */
	var $api_version = '1.0.13';

    /**
     * settings sections array
     *
     * @var array
     */
    private $settings_sections = array();
	
	/**
     * Settings sections array
     *
     * @var array
     */
    private $settings_sidebars = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    private $settings_fields = array();
	
	/**
	 * The Plugin prefix
	 * 
	 * @var string
	 */
	private $prefix;
	
	/**
	 * The Plugin domain
	 * 
	 * @var string
	 */
	private $domain;
	
	/**
	 * The Parent Plugin version
	 * 
	 * @var string
	 */
	private $version;

    /**
     * Singleton instance
     *
     * @var object
     */
    private static $_instance;
	
	/**
	 * Fire
	 *
	 */
    public function __construct() {
        add_action( 'admin_enqueue_scripts',	array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init',						array( $this, 'late_init' ), 89 );
    }
	
	/**
	 * Fire any actions needed a little late
	 *
	 * @return void
	 */
	function late_init() {
		if ( function_exists( 'EXTENDD_settings_init' ) ) {
			$extendd_settings_api = EXTENDD_settings_init();
			add_action( $this->prefix . '_settings_sidebars',	array( $extendd_settings_api, 'extendd_plugins_sidebar' ), 11 );
		} else {
			add_action( $this->prefix . '_settings_sidebars',	array( $this, 'extendd_plugins_sidebar' ), 11 );
		}
		
		add_action( 'admin_notices',			array( $this, 'show_notifications' ) );
		add_action( 'admin_init',				array( $this, 'notification_ignore' ) );
	}
	
	/**
	 * Set parent prefix
	 * 
	 * @param string $prefix
	 * @return void
	 */
	public function set_prefix( $prefix ) {
		$this->prefix = $prefix;
	}
	
	/**
	 * Set parent domain
	 * 
	 * @param string $domain
	 * @return void
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}
	
	/**
	 * Set parent version
	 * 
	 * @param string $version
	 * @return void
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts( $hook ) {
		if ( 'settings_page_' . $this->domain !== $hook )
			return;
			
		/* Core */
		if ( function_exists( 'wp_enqueue_media' ) ) wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		
		/* jQuery Chosen */
		wp_enqueue_script( 'jquery-chosen', plugins_url( 'assets/js/chosen.jquery.min.js', CUSTOM_LOGIN_FILE ), array( 'jquery' ), '1.0.0', false );
		wp_enqueue_style( 'jquery-chosen', plugins_url( 'assets/css/chosen.css', CUSTOM_LOGIN_FILE ), false, '1.0.0', 'screen' );
		
		/* Admin */
		wp_enqueue_script( $this->domain, plugins_url( 'assets/js/admin.js', CUSTOM_LOGIN_FILE ), array( 'jquery' ), $this->version, false );
		wp_enqueue_style( $this->domain, plugins_url( 'assets/css/admin.css', CUSTOM_LOGIN_FILE ), false, $this->version, 'screen' );
		
		/* Genericons */
		wp_enqueue_style( 'genericons', plugins_url( 'assets/css/genericons.css', CUSTOM_LOGIN_FILE ), false, '3.0.3', 'screen' );
    }

    /**
     * Set settings sections
     *
     * @param array   $sections setting sections array
     */
    function set_sections( $sections ) {
		$sections = apply_filters( $this->prefix . '_add_settings_sections', $sections );				
        $this->settings_sections = $sections;

        return $this;
    }

    /**
     * Add a single section
     *
     * @param array   $section
     */
    function add_section( $section ) {
        $this->settings_sections[] = $section;

        return $this;
    }

    /**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
		$fields = apply_filters( $this->prefix . '_add_settings_fields', $fields );
        $this->settings_fields = $fields;

        return $this;
    }

    function add_field( $section, $field ) {
        $defaults = array(
            'name'	=> '',
            'label' => '',
            'desc'	=> '',
            'type'	=> 'text'
        );

        $args = wp_parse_args( $field, $defaults );
        $this->settings_fields[$section][] = $args;

        return $this;
    }

    /**
     * Add a single section
     *
     * @param array   $section
     */
    function add_sidebar( $sidebar = array() ) {
		$sidebar = apply_filters( $this->prefix . '_add_settings_sidebar', $sidebar );
		if ( !empty( $sidebar ) ) {
        	$this->settings_sidebars[] = $sidebar;
		}
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    function admin_init() {
        //register settings sections
        foreach ( $this->settings_sections as $section ) {
            if ( false == get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }

            add_settings_section( $section['id'], $section['title'], '__return_false', $section['id'] );
        }

        //register settings fields
        foreach ( $this->settings_fields as $section => $field ) {
            foreach ( $field as $option ) {

                $type = isset( $option['type'] ) ? $option['type'] : 'text';

                $args = array(
                    'id'				=> $option['name'],
                    'desc' 				=> isset( $option['desc'] ) ? $option['desc'] : '',
                    'name' 				=> $option['label'],
                    'section' 			=> $section,
                    'size' 				=> isset( $option['size'] ) ? $option['size'] : null,
                    'options' 			=> isset( $option['options'] ) ? $option['options'] : '',
                    'std' 				=> isset( $option['default'] ) ? $option['default'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                );
				$args = wp_parse_args( $args, $option );
                add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], array( $this, 'callback_' . $type ), $section, $section, $args );
            }
        }

        // creates our settings in the options table
        foreach ( $this->settings_sections as $section ) {
            register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
        }
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_text( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html  = sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );		
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_text_array( $args ) {
		static $counter = 0;
		
        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
				
		$html  = '<ul style="margin-top:0">';
		
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $val ) {
				$html .= '<li>';
        		$html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s][]" value="%4$s" data-key="%5$s"/>', $size, $args['section'], $args['id'], esc_attr( $val ), $key );
				$html .= sprintf( '<a href="#" class="button dodelete-%1$s[%2$s]">-</a>', $args['section'], $args['id'] );
				$html .= '</li>';
			}
		} else {
			$html .= '<li>';
        	$html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s][]" value="%4$s" data-key="0" data-array="false"/>', $size, $args['section'], $args['id'], esc_attr( $value ) );
			$html .= sprintf( '<a href="#" class="button dodelete-%1$s[%2$s]">-</a>', $args['section'], $args['id'] );
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		$html .= sprintf( '<a href="#" class="button docopy-%1$s[%2$s]">+</a>', $args['section'], $args['id'] );
		
		$counter++;
		ob_start(); ?>
		<script>
		jQuery(document).ready(function($) {
			$('body').on('click', 'a[class^="button docopy-"]', function(e) {
				e.preventDefault();
				
				var $this = $(this).prev().children();
				//console.log($this);return;
				var clone = $('input[id="' + $this.children().prop('id') + '"]');
				var value = clone.data('key');
				var newValue = parseInt(value) + 1;

				//console.log( clone );
				var newInput = $this.last().clone();
				newInput.insertAfter( clone.parent().last() );
				newInput.children().val('').data('key',newValue);
				return false;
			});
			$('body').on('click', 'a[class^="button dodelete-"]', function(e) {
				e.preventDefault();
			//	console.log(this);
				
				$(this).parent().remove();
			});
		});
		</script><?php
		
		$html .= 1 === $counter ? ob_get_clean() : '';
				
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_colorpicker( $args ) {
		static $counter = 0;
		
        $value	 = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$check	 = esc_attr( $this->get_option( $args['id'] . '_checkbox', $args['section'], $args['std'] ) );
        $opacity = esc_attr( $this->get_option( $args['id'] . '_opacity', $args['section'], $args['std'] ) );
        $size	 = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'small';
		$opaque_options = array( '1', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1', '0', );
		
		/* Color */
        $html  = sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" style="float:left"/>', $size, $args['section'], $args['id'], $value );
		
		/* Allow Opacity */
		$html .= '<div class="checkbox-wrap">';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] . '_checkbox' );
        $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['section'], $args['id'] . '_checkbox', $check, checked( $check, 'on', false ) );
        $html .= sprintf( __( '<label for="%1$s[%2$s]">Opacity</label>', $this->domain ), $args['section'], $args['id'] . '_checkbox' );
        $html .= '</div>';
		
		/* Opacity */
       // $html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" style="margin-left:70px;%5$s" />', $size, $args['section'], $args['id'] . '_opacity', $opacity, ( 'on' !== $check ? 'display:none;' : '' ) );
	   $html .= sprintf( '<select class="%1$s%4$s" name="%2$s[%3$s]" id="%2$s[%3$s]" style="margin-left:70px;">', $size, $args['section'], $args['id'] . '_opacity', ( 'on' !== $check ? ' hidden' : '' ) );
        foreach ( $opaque_options as $key ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $opacity, $key, false ), $key );
        }
        $html .= sprintf( '</select>' );
		
		$counter++;
		ob_start(); ?>
        <script>
		jQuery(document).ready(function($) {
			var myOptions = {
				// you can declare a default color here,
				// or in the data-default-color attribute on the input
				defaultColor: false,
				// a callback to fire whenever the color changes to a valid color
				change: function(event, ui){},
				// a callback to fire when the input is emptied or an invalid color
				clear: function() {},
				// hide the color picker controls on load
				hide: true,
				// show a group of common colors beneath the square
				// or, supply an array of colors to customize further
				palettes: true
			};
			$('input[name="<?php echo $args['section'] . '[' . $args['id'] . ']'; ?>"]').wpColorPicker();
		   
		    $('select[name="<?php echo $args['section'] . '[' . $args['id'] . '_opacity]'; ?>"]').removeClass('hidden').chosen().addClass('hidden');
			if ( $('select[name="<?php echo $args['section'] . '[' . $args['id'] . '_opacity]'; ?>"]').hasClass('hidden') ) {
		    	$('#<?php echo str_replace( '[', '_', $args['section'] . '[' . $args['id'] . '_opacity' ); ?>__chosen').hide();
			}
			
		    $('input[name="<?php echo $args['section'] . '[' . $args['id'] . '_checkbox]'; ?>"]').on('change', function() {
		    	//$('select[name="<?php echo $args['section'] . '[' . $args['id'] . '_opacity]'; ?>"]').toggle();
		    	$('#<?php echo str_replace( '[', '_', $args['section'] . '[' . $args['id'] . '_opacity' ); ?>__chosen').toggle();
			});
		});
		</script><?php
		$html .= 1 === $counter ? ob_get_clean() : '';
		
		/* Description */
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_checkbox( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html  = '<div class="checkbox-wrap">';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
        $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['section'], $args['id'], $value, checked( $value, 'on', false ) );
        $html .= sprintf( '<label for="%1$s[%2$s]"></label>', $args['section'], $args['id'] );
        $html .= '</div>';
        $html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_multicheck( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );

        $html  = '<div class="checkbox-wrap">';
        $html .= '<ul>';
        foreach ( $args['options'] as $key => $label ) {
            $checked = isset( $value[$key] ) ? $value[$key] : '0';
        	$html .= '<li>';
            $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s"%4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%4$s]" title="%3$s"> %3$s</label>', $args['section'], $args['id'], $label, $key );
        	$html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_radio( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );

        $html  = '<div class="radio-wrap">';
        $html .= '<ul>';
        foreach ( $args['options'] as $key => $label ) {
        	$html .= '<li>';
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%4$s]" title="%3$s"> %3$s</label><br>', $args['section'], $args['id'], $label, $key );
        	$html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';


        $html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_select( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }
        $html .= sprintf( '</select>' );
		
		ob_start(); ?>
        <script>
		jQuery(document).ready(function($) {
		    $('select[name="<?php echo $args['section'] . '[' . $args['id'] . ']'; ?>"]').chosen();
		});
		</script><?php
		
		$html .= ob_get_clean();
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_textarea( $args ) {

        $value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		//$value = wp_specialchars_decode( stripslashes( $this->get_option( $args['id'], $args['section'], $args['std'] ) ), 1, 0, 1 );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], stripslashes( $value ) );
        $html .= sprintf( '<br><span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_html( $args ) {
        echo $args['desc'];
    }

    /**
     * Displays a rich text textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_wysiwyg( $args ) {

        $value	= wpautop( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size	= isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : '500px';

        echo '<div style="width: ' . $size . ';">';

        wp_editor( $value, $args['section'] . '[' . $args['id'] . ']', array( 'teeny' => true, 'textarea_rows' => 10 ) );

        echo '</div>';

        echo sprintf( '<br><span class="description"> %s</span>', $args['desc'] );
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_file( $args ) {
		static $counter = 0;
		
        $value	= esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size	= isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id		= $args['section']  . '[' . $args['id'] . ']';
        $html 	= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html  .= '<input type="button" class="button extendd-browse" id="'. $id .'_button" value="Browse" style="margin-left:5px" />';
        $html  .= '<input type="button" class="button extendd-clear" id="'. $id .'_clear" value="Clear" style="margin-left:5px" />';
		
		$counter++;
		if ( 1 === $counter ) {
			ob_start(); ?>
			<script>
			jQuery(document).ready(function($) {			
				// WP 3.5+ uploader
				var file_frame;
				var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
				var set_to_post_id = <?php echo isset( $args['page_id'] ) ? $args['page_id'] : '0'; ?>; // Set this
				window.formfield = '';
				
				$(document.body).on('click', 'input[type="button"].button.extendd-browse', function(e) {
		
					e.preventDefault();
		
					var button = $(this);
					
					window.formfield = $(this).closest('td');
		
					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
						file_frame.open();
						return;
					} else {
						// Set the wp.media post id so the uploader grabs the ID we want when initialised
						wp.media.model.settings.post.id = set_to_post_id;
					}
		
					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media({
						frame: 'post',
						state: 'insert',
						title: button.data( 'uploader_title' ),
						button: {
							text: button.data( 'uploader_button_text' ),
						},
						library: {
							type: 'image',
						},
						multiple: false  // Set to true to allow multiple files to be selected
					});
		
					file_frame.on( 'menu:render:default', function(view) {
						// Store our views in an object.
						var views = {};
		
						// Unset default menu items
						view.unset('library-separator');
						view.unset('gallery');
						view.unset('featured-image');
						view.unset('embed');
		
						// Initialize the views in our view object.
						view.set(views);
					});
		
					// When an image is selected, run a callback.
					file_frame.on( 'insert', function() {	
						var attachment = file_frame.state().get('selection').first().toJSON();
					//	console.log(attachment);
					
						window.formfield.find('input[type="text"]').val(attachment.url);
						window.formfield.find('#<?php echo $id; ?>_preview').html('<div class="img" style="width:250px"><img src="'+attachment.url+'" alt="" /><a href="#" class="remove_file_button" rel="<?php echo $id; ?>">Remove Image</a></div>');
					});
		
					// Finally, open the modal
					file_frame.open();
				});
				
				// WP 3.5+ uploader
				var file_frame;
				window.formfield = ''; 
				
				$('input[type="button"].button.extendd-clear').on('click', function(e) {  
					e.preventDefault();
					$(this).closest('td').find('input[type="text"]').val('');
					$(this).closest('td').find('#' + $(this).prop('id').replace( '_clear', '_preview') + ' div.image').remove();
				});
				$('a.remove_file_button').on( 'click', function(e) {
					e.preventDefault();
					$(this).closest('td').find('input[type="text"]').val('');
					$(this).parent().slideUp().remove();
				});
			});
			</script><?php
			$html .= ob_get_clean();
		}
        $html .= sprintf( '<br><span class="description"> %s</span>', $args['desc'] );
		
		/* Image */
		$html .= '<div id="' . $id . '_preview" class="' . $id . '_preview">';	
			if ( $value != '' ) { 
				$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
				if ( $check_image ) {
					$html .= '<div class="img" style="display:none">';
					$html .= '<img src="' . $value . '" alt="" />';
					$html .= '<a href="#" class="remove_file_button" rel="' . $id . '">Remove Image</a>';
					$html .= '</div>';
				}
			}
		$html .= '</div>';

        echo $html;
    }

    /**
     * Displays a password field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_password( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    /**
     * Sanitize callback for Settings API
     */ 
    function sanitize_options( $options ) {
		delete_transient( $this->prefix . '_style' );
		delete_transient( $this->prefix . '_script' );
		
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
        foreach( $this->settings_fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug )
                    continue;
                // Return the callback name 
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }
        return false; 
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    function get_option( $option, $section, $default = '' ) {

        $options = get_option( $section );

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation() {
        $html = '<h2 class="nav-tab-wrapper">';

        foreach ( $this->settings_sections as $tab ) {
            $html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'] );
        }

        $html .= '</h2>';

        echo $html;
    }
	
	/**
     * Helper function to make remote calls
	 *
	 * @since 2.2
     */
    function wp_remote_get_set_transient( $url = false, $transient, $type = 'message', $cache_time = null ) {
		if ( !$url ) return;
		
		if ( is_null( $cache_time ) ) {
			if ( defined( 'DAY_IN_SECONDS' ) )
				$cache_time = DAY_IN_SECONDS;
			else
				$cache_time = 24 * 60 * 60;
		}
		
		if ( false === ( $output = get_transient( $transient ) ) ) {
			$site = wp_remote_get( $url, array( 'timeout' => 15, 'sslverify' => false ) );
			if ( !is_wp_error( $site ) ) {
				if ( isset( $site['body'] ) && strlen( $site['body'] ) > 0 ) {
					$output = json_decode( wp_remote_retrieve_body( $site ) );
					
					// For when I mess up the JSON or github is down.
					if ( is_wp_error( $output ) || empty( $output->$type ) )
						return false;
						
					set_transient( $transient, $output, $cache_time ); // Cache for two weeks
					update_option( $transient . '_message', $output->$type ); // Update the message
					
					// Return the data
					return $output;
				}
			} else {
				// Error, lets return!
				return false;
			}
		}
		return $output;
		
	}

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
	 *
	 * @uses	http://codex.wordpress.org/Transients_API#Using_Time_Constants
     */
    function show_notifications() {
		$transient		= $this->prefix . '_announcement';	
		$ignore			= $this->prefix . '_ignore_announcement';		
		$old_message	= get_option( $transient . '_message' );
		$user_meta		= get_user_meta( get_current_user_id(), $ignore, true );		
		
//		delete_user_meta( get_current_user_id(), $ignore, 1 );
//		delete_transient( $transient );
//		delete_option( $this->prefix . '_announcement_message' );
		
		/* Current user can */
		if ( !current_user_can( 'manage_options' ) )
			return;
		
		$announcement = $this->wp_remote_get_set_transient( 'https://raw.github.com/thefrosty/custom-login/master/extensions.json', $transient, 'message' );
		
		//print_r( $announcement );
		
		if ( false === $announcement ) return;
			
		if ( trim( $old_message ) !== trim( $announcement->message ) && !empty( $old_message ) ) {
			delete_user_meta( get_current_user_id(), $ignore, 1 );
			delete_transient( $transient );
			delete_option( $transient . '_message' );
			//echo 'test';
		}
		
		$html  = '<div class="updated" data-old-message="' . esc_attr( $old_message ) . '" data-announcement="' . esc_attr( $announcement->message ) . '"><p>'; 
		$html .= sprintf( __( '%1$s | <a href="%2$s">Dismiss notice</a>', $this->domain ), $announcement->message, esc_url( add_query_arg( $ignore, wp_create_nonce( $ignore ), admin_url( 'options-general.php?page=custom-login' ) ) ) );
		$html .= '</p></div>';
		
		if ( !$user_meta && 1 !== $user_meta )
			echo $html;
	}
	
	/**
	 * Remove notification
	 *
	 * @return void
	 */
	function notification_ignore() {
		$ignore  = $this->prefix . '_ignore_announcement';
		
		//if ( isset( $_GET[$ignore] ) ) echo $_GET[$ignore]; exit;
		
		if ( !isset( $_GET[$ignore] ) )
			return;
			
		// Check nonce
	    check_admin_referer( $ignore, $ignore );
		
		/* If user clicks to ignore the notice, add that to their user meta */
		add_user_meta( get_current_user_id(), $ignore, 1, true );
	}

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    function show_forms() { ?>
        <div class="section col-group">
            <div class="postbox col span_2_of_3">
                <?php foreach ( $this->settings_sections as $form ) { ?>
                    <div id="<?php echo $form['id']; ?>" class="group">
                        <form method="post" action="options.php">

                            <?php do_action( $this->prefix . '_form_top_' . $form['id'], $form ); ?>
                            <?php settings_fields( $form['id'] ); ?>
                            <div class="inside"><?php do_settings_sections( $form['id'] ); ?></div>
                            <?php do_action( $this->prefix . '_form_bottom_' . $form['id'], $form ); ?>

                            <div style="padding-left: 10px">
                                <?php submit_button(); ?>
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        <div class="col span_1_of_3" style="margin-top:0">
        	<?php do_action( $this->prefix . '_settings_sidebars', $this->settings_sidebars ); ?>
        </div>
        
        </div>
        <br class="clear">
        <?php
    }

    /**
     * Tabbable JavaScript codes
     *
     * This code uses localstorage for displaying active tabs
     */
    function inline_jquery() { ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Switches option sections
		$('.group').hide();
		var activetab = '';
		if (typeof(localStorage) != 'undefined' ) {
			activetab = localStorage.getItem("activetab");
		}
		if (activetab != '' && $(activetab).length ) {
			$(activetab).fadeIn();
			$(activetab + '_sidebar').fadeIn();
		} else {
			$('.group:first').fadeIn();
			$('.metabox-holder.group:first').fadeIn();
		}
		$('.group .collapsed').each(function(){
			$(this).find('input:checked').parent().parent().parent().nextAll().each(
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
					return false;
				}
				$(this).filter('.hidden').removeClass('hidden');
			});
		});

		if (activetab != '' && $(activetab + '-tab').length ) {
			$(activetab + '-tab').addClass('nav-tab-active');
		}
		else {
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}
		$('.nav-tab-wrapper a').on('click',function(e) {
			$('.nav-tab-wrapper a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active').blur();
			var clicked_group = $(this).attr('href');
			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem("activetab", $(this).attr('href'));
			}
			$('.group').hide();
			$(clicked_group).fadeIn();
			$(clicked_group + '_sidebar').fadeIn();
			e.preventDefault();
		});
	<?php if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) { ?>
		
		setTimeout( function() {
			$('#setting-error-settings_updated, #setting-error-transitent_deleted').fadeOut('slow');
		}, 4000 );
	<?php } ?>
	});
</script><?php
    }

	/**
	 * Create a potbox widget.
	 *
	 * @param 	string $id      ID of the postbox.
	 * @param 	string $title   Title of the postbox.
	 * @param 	string $content Content of the postbox.
	 */
	public function postbox( $id, $title, $content, $group = false ) {
		?>
        <div class="metabox-holder<?php if ( $group ) echo ' group'; ?>" id="<?php echo $id; ?>">
            <div class="postbox">
            <h3><?php echo $title; ?></h3>
            <div class="inside"><?php echo $content; ?></div>
            </div>
        </div>
        <?php
	}
	
	/**
	 * Fetch RSS items from the feed.
	 *
	 * @param 	int    $num  Number of items to fetch.
	 * @param 	string $feed The feed to fetch.
	 * @return 	array|bool False on error, array of RSS items on success.
	 */
	public function fetch_rss_items( $num, $feed ) {
		if ( !function_exists( 'fetch_feed' ) )
			include_once( ABSPATH . WPINC . '/feed.php' );
			
		$rss = fetch_feed( $feed );

		// Bail if feed doesn't work
		if ( !$rss || is_wp_error( $rss ) )
			return false;

		$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );

		// If the feed was erroneous 
		if ( !$rss_items ) {
			$md5 = md5( $feed );
			delete_transient( 'feed_' . $md5 );
			delete_transient( 'feed_mod_' . $md5 );
			$rss       = fetch_feed( $feed );
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
		}

		return $rss_items;
	}

	/**
	 * Box with latest plugins from Extendd.com for sidebar
	 */
	function extendd_plugins_sidebar( $args ) {
		
		$defaults = array(
			'items' => 6,
			'feed' 	=> 'http://extendd.com/feed/?post_type=download',
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$rss_items = $this->fetch_rss_items( $args['items'], $args['feed'] );
		
		$content = '<ul>';
		if ( !$rss_items ) {
			$content .= '<li>' . __( 'Error fetching feed', $this->domain ) . '</li>';
		} else {
			foreach ( $rss_items as $item ) {
				$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), null, 'display' ) );
				$content .= '<li>';
				$content .= '<a class="rsswidget" href="' . $url . '#utm_source=wpadmin&utm_medium=sidebarwidget&utm_term=newsitem&utm_campaign=' . $this->prefix . 'settingsapi">' . esc_html( $item->get_title() ) . '</a> ';
				$content .= '</li>';
			}
		}
		$content .= '</ul>';
		$content .= '<ul class="social">';
		$content .= '<li class="facebook"><span class="genericon genericon-facebook"></span><a href="https://www.facebook.com/WPExtendd">' . __( 'Like Extendd on Facebook', $this->domain ) . '</a></li>';
		$content .= '<li class="twitter"><span class="genericon genericon-twitter"></span><a href="http://twitter.com/WPExtendd">' . __( 'Follow Extendd on Twitter', $this->domain ) . '</a></li>';
		$content .= '<li class="twitter"><span class="genericon genericon-twitter"></span><a href="http://twitter.com/TheFrosty">' . __( 'Follow Austin on Twitter', $this->domain ) . '</a></li>';
		$content .= '<li class="googleplus"><span class="genericon genericon-googleplus"></span><a href="https://plus.google.com/113609352601311785002/">' . __( 'Circle Extendd on Google+', $this->domain ) . '</a></li>';
		$content .= '<li class="email"><span class="genericons genericons-mail"></span><a href="http://eepurl.com/vi0bz">' . __( 'Subscribe via email', $this->domain ) . '</a></li>';

		$content .= '</ul>';
		$this->postbox( 'extenddlatest', __( 'Latest plugins from Extendd.com', $this->domain ), $content );
	}

}
endif;