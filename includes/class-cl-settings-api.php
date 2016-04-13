<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Login Settings API
 */
class CL_Settings_API {

    /**
     * Version
     */
    var $api_version = '2.1.0';

    /**
     * @var array
     */
    private $settings_sections = array();
    private $settings_fields = array();
    private $settings_sidebars = array();
    private $localize_array = array();

    /**
     * @var array
     */
    private $settings = array();

    /**
     * Fire away captain!
     */
    public function __construct( $sections = array(), $fields = array(), $args = array() ) {

        $this->settings = $args;

        if ( ! empty( $sections ) ) {
            $this->set_sections( $sections );
        }

        if ( ! empty( $fields ) ) {
            $this->set_fields( $fields );
        }

        add_action( 'load-' . $this->settings['menu_page'], array( $this, 'init' ), 89 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_footer', array( $this, 'wp_localize_script' ), 99 );
        add_action( 'wp_ajax_' . $this->settings['prefix'] . '_get_form', array( $this, 'get_form' ), 99 );
        add_action( 'wp_ajax_' . $this->settings['prefix'] . '_activate_check', array( $this, 'activate_check_ajax' ) );
    }

    /**
     * Fire any actions needed a little late
     *
     * @return void
     */
    public function init() {

        add_action( 'admin_notices', array( $this, 'upgrade_notices' ) );
        add_action( $this->settings['prefix'] . '_sticky_admin_notice', array(
            $this,
            'sticky_admin_notice_social_links',
        ), 10 );
        add_action( $this->settings['prefix'] . '_before_submit_button', array( $this, 'is_active_toggle' ), 10 );
        add_action( $this->settings['prefix'] . '_settings_sidebars', array( $this, 'about_the_author' ), 19 );
        add_action( $this->settings['prefix'] . '_settings_sidebars', array( $this, 'sidebar_feed' ), 20 );
    }

    /**
     * Enqueue scripts and styles
     */
    public function admin_enqueue_scripts( $hook ) {
        if ( 'settings_page_' . $this->settings['domain'] !== $hook ) {
            return;
        }

        /* Core */
        wp_enqueue_media();
        wp_enqueue_script( array( 'wp-color-picker', 'plugin-install' ) );
        wp_enqueue_style( array( 'wp-color-picker', 'thickbox', 'plugin-install' ) );

        /* jQuery Chosen */
        wp_enqueue_script( 'chosen', plugins_url( 'js/chosen.jquery.min.js', $this->settings['file'] ), array( 'jquery' ), '1.3.0', true );
        wp_enqueue_style( 'chosen', plugins_url( 'css/chosen/chosen.min.css', $this->settings['file'] ), null, '1.3.0', 'screen' );

        /* jQuery Sticky */
        wp_enqueue_script( 'sticky', plugins_url( 'js/jquery.sticky.js', $this->settings['file'] ), array( 'jquery' ), '1.0.0', true );

        /* Ace */
        wp_enqueue_script( 'ace', plugins_url( 'js/ace/src-min-noconflict/ace.js', $this->settings['file'] ), null, '20.12.14', true );

        /* Dashicons */
        wp_enqueue_style( 'dashicons' );

        /* Admin */
        wp_enqueue_script( $this->settings['domain'], plugins_url( 'js/admin.js', $this->settings['file'] ), array(
            'jquery',
            'jquery-form',
        ), $this->settings['version'], true );
        wp_enqueue_style( $this->settings['domain'], plugins_url( 'css/admin.css', $this->settings['file'] ), false, $this->settings['version'], 'screen' );

        do_action( "{$this->settings['domain']}_admin_enqueue_scripts" );
    }

    /**
     * Localize our script array.
     */
    public function wp_localize_script() {
        $this->localize_array['prefix']  = $this->settings['prefix'];
        $this->localize_array['blog_id'] = get_current_blog_id();
        $this->localize_array['nonce']   = wp_create_nonce( $this->settings['nonce'] );
        wp_localize_script( $this->settings['domain'], 'cl_settings_api', $this->localize_array );
    }

    /**
     * Set settings sections
     *
     * @param array $sections setting sections array
     */
    public function set_sections( $sections ) {

        $sections                = apply_filters( $this->settings['prefix'] . '_add_settings_sections', $sections );
        $this->settings_sections = $sections;

        return $this;
    }

    /**
     * Add a single section
     *
     * @param array $section
     */
    public function add_section( $section ) {

        $this->settings_sections[] = $section;

        return $this;
    }

    /**
     * Set settings fields
     *
     * @param array $fields settings fields array
     */
    public function set_fields( $fields ) {

        $fields                = apply_filters( $this->settings['prefix'] . '_add_settings_fields', $fields );
        $this->settings_fields = $fields;

        return $this;
    }

    /**
     * Add a single field
     *
     * @param array $section
     * @param array $field
     */
    public function add_field( $section, $field ) {

        $defaults = array(
            'name'  => '',
            'label' => '',
            'desc'  => '',
            'type'  => 'text',
        );

        $args                                = wp_parse_args( $field, $defaults );
        $this->settings_fields[ $section ][] = $args;

        return $this;
    }

    /**
     * Add a single section
     *
     * @param array $section
     */
    public function add_sidebar( $sidebar = array() ) {

        $sidebar = apply_filters( $this->settings['prefix'] . '_add_settings_sidebar', $sidebar );
        if ( ! empty( $sidebar ) ) {
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
    public function admin_init() {

        //register settings sections
        foreach ( $this->settings_sections as $section ) {
            if ( false == get_option( $section['id'] ) && ( isset( $section['option'] ) && false !== $section['option'] ) ) {
                add_option( $section['id'] );
            }

            add_settings_section( $section['id'], $section['title'], '__return_false', $section['id'] );
        }

        //register settings fields
        foreach ( $this->settings_fields as $section => $field ) {
            foreach ( $field as $option ) {

                $type = isset( $option['type'] ) ? $option['type'] : 'text';

                $args = array(
                    'id'       => $option['name'],
                    'desc'     => isset( $option['desc'] ) ? $option['desc'] : '',
                    'name'     => $option['label'],
                    'section'  => $section,
                    'size'     => isset( $option['size'] ) ? $option['size'] : null,
                    'options'  => isset( $option['options'] ) ? $option['options'] : '',
                    'default'  => isset( $option['default'] ) ? $option['default'] : '',
                    'sanitize' => isset( $option['sanitize'] ) ? $option['sanitize'] : '',
                    'callback' => isset( $option['class'] ) ? $option['class'] : $this,
                );
                $args = wp_parse_args( $args, $option );

                add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], array(
                    $args['callback'],
                    'callback_' . $type,
                ), $section, $section, $args );
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
     * @param array $args settings field args
     *
     * @updated    2.0.2
     */
    function callback_text( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type  = isset( $args['type'] ) && ! is_null( $args['type'] ) ? $args['type'] : 'text';

        $html = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s">', $type, $size, $args['section'], $args['id'], $value );
        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array $args settings field args
     *
     * @since        2.0.2
     */
    function callback_text_number( $args ) {

        $args['type'] = 'number';
        $this->callback_text( $args );
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_text_array( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['default'] );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = '<ul style="margin-top:0">';

        if ( is_array( $value ) ) {
            foreach ( $value as $key => $val ) {
                $html .= '<li>';
                $html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s][]" value="%4$s" data-key="%5$s">', $size, $args['section'], $args['id'], esc_attr( $val ), $key );
                $html .= sprintf( '<a href="#" class="button dodelete-%1$s[%2$s]">-</a>', $args['section'], $args['id'] );
                $html .= '</li>';
            }
        } else {
            $html .= '<li>';
            $html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s][]" value="%4$s" data-key="0" data-array="false">', $size, $args['section'], $args['id'], esc_attr( $value ) );
            $html .= sprintf( '<a href="#" class="button dodelete-%1$s[%2$s]">-</a>', $args['section'], $args['id'] );
            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= sprintf( '<a href="#" class="button docopy-%1$s[%2$s]">+</a>', $args['section'], $args['id'] );

        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_colorpicker( $args ) {

        $value   = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $check   = esc_attr( $this->get_option( $args['id'] . '_checkbox', $args['section'], $args['default'] ) );
        $opacity = esc_attr( $this->get_option( $args['id'] . '_opacity', $args['section'], $args['default'] ) );
        $size    = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'small';
        $options = array( '1', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1', '0', );
        $class   = 'on' != $check ? ' hidden' : '';

        /* Localize the array */
        $this->localize_array['callback_colorpicker'][] = array( 'id' => $args['id'], 'section' => $args['section'] );

        /* Color */
        $html = '<div class="cl-colorpicker-wrap">';
        $html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" style="float:left">', $size, $args['section'], $args['id'], $value );

        /* Allow Opacity */
        $html .= '<div class="checkbox-wrap">';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" >', $args['section'], $args['id'] . '_checkbox' );
        $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s >', $args['section'], $args['id'] . '_checkbox', $check, checked( $check, 'on', false ) );
        $html .= sprintf( __( '<label for="%1$s[%2$s]">Opacity</label>', $this->settings['domain'] ), $args['section'], $args['id'] . '_checkbox' );
        $html .= '</div>';

        /* Opacity */
        $html .= sprintf( '<select class="%1$s%4$s" name="%2$s[%3$s]" id="%2$s[%3$s]" style="margin-left:70px;">', $size, $args['section'], $args['id'] . '_opacity', $class );
        foreach ( $options as $key ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $opacity, $key, false ), $key );
        }
        $html .= '</select>';
        $html .= '<br class="clear">';
        $html .= '</div>';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_checkbox( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );

        $html = '<div class="checkbox-wrap">';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" >', $args['section'], $args['id'] );
        $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s >', $args['section'], $args['id'], $value, checked( $value, 'on', false ) );
        $html .= sprintf( '<label for="%1$s[%2$s]"></label>', $args['section'], $args['id'] );
        $html .= '</div>';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_multicheck( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['default'] );

        $html = '<div class="checkbox-wrap">';
        $html .= '<ul>';
        foreach ( $args['options'] as $key => $label ) {
            $checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
            $html .= '<li>';
            $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s"%4$s >', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%4$s]" title="%3$s"> %3$s</label>', $args['section'], $args['id'], $label, $key );
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_radio( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['default'] );

        $html = '<div class="radio-wrap">';
        $html .= '<ul>';
        foreach ( $args['options'] as $key => $label ) {
            $html .= '<li>';
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s >', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%4$s]" title="%3$s"> %3$s</label><br>', $args['section'], $args['id'], $label, $key );
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_select( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        /* Localize the array */
        $this->localize_array['callback_select'][] = array( 'id' => $args['id'], 'section' => $args['section'] );

        $html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }
        $html .= sprintf( '</select>' );

        $html .= ! empty( $args['desc'] ) ? sprintf( '<br><span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_textarea( $args ) {

        $value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $extra = isset( $args['extra'] ) && is_array( $args['extra'] ) ? $args['extra'] : null;
        $param = '';

        if ( null !== $extra ) {
            foreach ( $extra as $p_key => $p_value ) {
                $param .= $p_key . '="' . $p_value . '"';
            }
        }

        $html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%5$s>%4$s</textarea>', $size, $args['section'], $args['id'], stripslashes( $value ), $param );
        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a HTML for a settings field
     *
     * @param array $args settings field args
     */
    function callback_html( $args ) {
        static $counter = 0;

        $html = isset( $args['desc'] ) ? sprintf( '<div class="section-%s-%d">%s</div><hr>', $args['section'], $counter, $args['desc'] ) : '';
        $counter ++;

        echo $html;
    }

    /**
     * Displays raw HTML for a settings field
     *
     * @param array $args settings field args
     */
    function callback_raw( $args ) {

        $html = isset( $args['desc'] ) ? sprintf( '<div class="raw-html">%s</div>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a rich text textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_wysiwyg( $args ) {

        $value = wpautop( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

        $html = sprintf( '<div style="width: %s">', $size );

        ob_start();
        wp_editor( $value, $args['section'] . '[' . $args['id'] . ']', array(
            'teeny'         => true,
            'textarea_rows' => 10,
        ) );

        $html .= ob_get_clean();
        $html .= '</div>';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<br><span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_file( $args ) {
        static $counter = 0;

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id    = $args['section'] . '[' . $args['id'] . ']';

        /* Localize the array */
        $this->localize_array['callback_file'][] = array( 'id' => $args['id'], 'section' => $args['section'] );

        $html = sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s">', $size, $args['section'], $args['id'], $value );
        $html .= '<input type="button" class="button ' . $args['id'] . '-browse" id="' . $id . '_button" value="Browse" style="margin-left:5px" >';
        $html .= '<input type="button" class="button ' . $args['id'] . '-clear" id="' . $id . '_clear" value="Clear" style="margin-left:5px" >';

        $html .= ! empty( $args['desc'] ) ? sprintf( '<br><span class="description"> %s</span>', $args['desc'] ) : '';

        /* Image */
        $html .= '<div id="' . $id . '_preview" class="' . $id . '_preview">';
        if ( $value != '' ) {
            $check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
            if ( $check_image ) {
                $html .= '<div class="img-wrapper">';
                $html .= '<img src="' . $value . '" alt="" >';
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
     * @param array $args settings field args
     */
    function callback_password( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['default'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s">', $size, $args['section'], $args['id'], $value );
        $html .= ! empty( $args['desc'] ) ? sprintf( '<span class="description"> %s</span>', $args['desc'] ) : '';

        echo $html;
    }

    /**
     * Sanitize callback for Settings API
     */
    function sanitize_options( $options ) {

        if ( is_null( $options ) ) {
            return $options;
        }

        do_action( $this->settings['prefix'] . '_before_sanitize_options', $options );

        foreach ( $options as $option_slug => $option_value ) {
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );

            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }

            // Treat everything that's not an array as a string
            if ( ! is_array( $option_value ) ) {
                $options[ $option_slug ] = sanitize_text_field( $option_value );
                continue;
            }
        }

        do_action( $this->settings['prefix'] . '_after_sanitize_options', $options );

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

        if ( empty( $slug ) ) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach ( $this->settings_fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }

                // Return the callback name
                return isset( $option['sanitize'] ) && is_callable( $option['sanitize'] ) ? $option['sanitize'] : false;
            }
        }

        return false;
    }

    /**
     * Outpute our settings HTML
     *
     */
    public function settings_html() { ?>

        <div class="cl-container">

            <div class="cl-header">
                <h3><?php _e( 'Custom Login', $this->settings['domain'] ); ?></h3>
                <span><?php echo $this->settings['version']; ?></span>
                <div>
                    <?php echo sprintf( __( 'A %s plugin', $this->settings['domain'] ), '<strong><a href="https://frosty.media/" target="_blank">Frosty Media</a></strong>' ); ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://twitter.com/Frosty_Media"><span
                            class="dashicons dashicons-twitter"></span></a>
                </div>
            </div><!-- #cl-header -->

            <div id="cl-notices">
                <h2></h2>
            </div><!-- #cl-text -->

            <div id="cl-sticky">
                <div class="wrap">
                    <div id="sticky-admin-notice">
                        <?php do_action( $this->settings['prefix'] . '_sticky_admin_notice' ); ?>
                    </div>
                    <div class="alignright">
                        <?php do_action( $this->settings['prefix'] . '_before_submit_button' ); ?>
                        <?php submit_button( __( 'Save Changes', $this->settings['domain'] ), 'primary', 'cl_save', false ); ?>
                    </div>
                    <br class="clear">
                </div>
            </div><!-- #cl-sticky -->

            <div class="cl-sidebar">
                <?php $this->show_navigation(); ?>
                <?php do_action( $this->settings['prefix'] . '_settings_sidebars', $this->settings_sidebars ); ?>
            </div><!-- #cl-header -->

            <div class="cl-main">
                <?php $this->show_forms(); ?>
            </div><!-- #cl-header -->

        </div><!-- #cl-wrapper -->
        <?php
    }

    /**
     * Show navigation as lists
     *
     * Shows all the settings section labels as list items
     */
    private function show_navigation() {

        $html = '<ul class="cl-sections-menu">';
        foreach ( $this->settings_sections as $tab ) {
            $html .= sprintf( '<li><a href="%1$s">%2$s</a></li>', isset( $tab['href'] ) ? $tab['href'] : '#' . $tab['id'], $tab['title'] );
        }
        $html .= '</ul>';

        echo $html;
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    private function show_forms() {

        foreach ( $this->settings_sections as $form ) {
            $form_id = $form['id']; ?>
        <div id="<?php echo $form_id; ?>" class="group">
            <form action="options.php" id="<?php echo $form_id; ?>form" method="post">
                <?php do_action( $this->settings['prefix'] . '_form_top_' . $form_id, $form ); ?>
                <?php settings_fields( $form_id ); ?>
                <?php do_settings_sections( $form_id ); ?>
                <?php do_action( $this->settings['prefix'] . '_form_bottom_' . $form_id, $form ); ?>
                <?php if ( isset( $form['submit'] ) && $form['submit'] ) {
                    submit_button( sprintf( __( 'Save %s', $this->settings['domain'] ), $form['title'] ) );
                } ?>
            </form>
            </div><?php
            #	var_dump( $form_id, get_option( $form_id ) );
        }
        do_action( $this->settings['prefix'] . '_after_settings_sections_form' );
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    public function get_form() {

        check_ajax_referer( $this->settings['nonce'], 'nonce' );

        if ( isset( $_POST['form_id'] ) ) {

            $setting_form          = array();
            $setting_form['error'] = 1;

            foreach ( $this->settings_sections as $form ) {
                $form_id = $form['id'];
                if ( str_replace( '#', '', $_POST['form_id'] ) !== $form_id ) {
                    continue;
                }
                ob_start(); ?>
            <form action="options.php" id="<?php echo $form_id; ?>form" method="post">
                <?php do_action( $this->settings['prefix'] . '_form_top_' . $form['id'], $form ); ?>
                <?php settings_fields( $form['id'] ); ?>
                <?php do_settings_sections( $form['id'] ); ?>
                <?php do_action( $this->settings['prefix'] . '_form_bottom_' . $form['id'], $form ); ?>
                <?php submit_button( sprintf( __( 'Save %s Changes', $this->settings['domain'] ), $form_id ) ); ?>
                </form><?php
                $setting_form['error'] = 0;
                $setting_form['html']  = ob_get_clean();
            }

            header( 'Content-Type: application/json' );
            echo json_encode( $setting_form );
            die();
        }
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    public function activate_check_ajax() {

        if ( empty( $_POST ) || ! check_ajax_referer( $this->settings['nonce'], 'nonce', false ) ) {
            wp_send_json_error();
        }

        $settings     = CL_Common::get_options( 'general' );
        $active_value = isset( $_POST['active_value'] ) && 'true' == $_POST['active_value'] ? 'on' : 'off';

        if ( $settings['active'] !== $active_value ) {
            $settings['active'] = $active_value;

            if ( update_option( CUSTOM_LOGIN_OPTION . '_general', $settings ) ) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        }

        wp_send_json_success();
    }

    /**
     * Create a potbox widget.
     *
     * @param    string $id ID of the postbox.
     * @param    string $title Title of the postbox.
     * @param    string $content Content of the postbox.
     */
    public function postbox( $id, $title, $content, $group = false ) { ?>

    <div class="metabox-holder<?php if ( $group ) {
        echo ' group';
    } ?>" id="<?php echo $id; ?>">
        <div class="postbox">
            <h3><?php echo $title; ?></h3>
            <div class="inside"><?php echo $content; ?></div>
        </div>
        </div><?php
    }

    /**
     * Global 'active' checkbox notification.
     *
     * @ref    http://codepen.io/pklada/pen/jEGwMB
     */
    function is_active_toggle() { ?>
        <label class="tgl">
            <span class="tgl_input"></span>
		<span class="tgl_body">
			<span class="tgl_switch"></span>
			<span class="tgl_track">
				<span class="tgl_bgd"></span>
				<span class="tgl_bgd tgl_bgd-negative"></span>
			</span>
		</span>
        </label><?php
    }

    /**
     * Box with latest plugins from Extendd.com for sidebar
     */
    function about_the_author( $args ) {

        $content = sprintf( '%s: <a href="https://wordpress.org/support/view/plugin-reviews/custom-login" class="star-rating" target="_blank">
			<i class="dashicons dashicons-star-filled"></i>
			<i class="dashicons dashicons-star-filled"></i>
			<i class="dashicons dashicons-star-filled"></i>
			<i class="dashicons dashicons-star-filled"></i>
			<i class="dashicons dashicons-star-filled"></i>
			</a>', _x( 'Rate', 'rate; as in rate this plugin', $this->settings['domain'] ) );

        $content .= '<ul>';
        $content .= sprintf( '<li>%s: <a href="http://austin.passy.co" target="_blank">Austin Passy</a></li>', _x( 'Author', 'the author of this plugin', $this->settings['domain'] ) );
        $content .= sprintf( '<li>%s: <a href="https://twitter.com/TheFrosty" target="_blank">TheFrosty</a></li>', __( 'Twitter', $this->settings['domain'] ) );
        $content .= '</ul>';

        $content .= sprintf( __( '<small>If you have suggestions for a new add-on, feel free to open a support request on <a href="%s" target="_blank">GitHub</a>. Want regular updates? Follow me on <a href="%s" target="_blank">Twitter</a> or visit my <a href="%s" target="_blank">blog</a>.</small>' ),
            'https://github.com/thefrosty/custom-login/issues',
            'https://twitter.com/TheFrosty',
            'http://austin.passy.co'
        );

        $this->postbox( 'frosty-media-author', __( 'Custom Login', $this->settings['domain'] ), $content );
    }

    /**
     * Box with latest plugins from Extendd.com for sidebar
     */
    function sidebar_feed( $args ) {

        $defaults = array(
            'items' => 6,
            'feed'  => 'https://frosty.media/feed/?post_type=plugin&plugin_tag=custom-login-extension',
        );

        $args = wp_parse_args( $args, $defaults );

        $rss_items = CL_Common::fetch_rss_items( $args['items'], $args['feed'] );

        $content = '<ul>';
        if ( ! $rss_items ) {
            $content .= '<li>' . __( 'Error fetching feed', $this->settings['domain'] ) . '</li>';
        } else {
            foreach ( $rss_items as $item ) {
                $url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), null, 'display' ) );
                $content .= '<li>';
                $content .= '<a href="' . $url . '?utm_source=wpadmin&utm_medium=sidebarwidget&utm_term=newsite&utm_campaign=' . $this->settings['prefix'] . '_settings-api" target="_blank">' . esc_html( $item->get_title() ) . '</a>';
                $content .= '</li>';
            }
        }
        $content .= '</ul>';

        $this->postbox( 'custom-login-extensions', sprintf( __( 'Custom Login Extensions %s', $this->settings['domain'] ), '<small class="dashicons dashicons-external"></small>' ), $content );
    }

    /**
     * Display Upgrade Notices
     *
     * @access      private
     * @since       3.0.3
     * @return      void
     */
    public function upgrade_notices() {

        $show_upgrade_notice = false;

        // Version < 2.0
        if ( false !== get_option( 'custom_login_settings', false ) ) {
            $show_upgrade_notice = true;
        }

        // Version > 2.0
        if ( false !== get_option( 'custom_login', false ) ) {
            $show_upgrade_notice = true;
        }

        if ( $show_upgrade_notice && ( '' === get_option( CUSTOM_LOGIN_OPTION . '_general', '' ) ) ) {
            remove_action( 'admin_notice', array( CL_Settings_Upgrade::instance(), 'upgrade_notices' ) );
            printf(
                '<div class="error"><p>' . esc_html__( 'Custom Login has detected old settings. If you wish to use them please run %sthis%s script before making any changes below.', CUSTOM_LOGIN_DIRNAME ) . '</p></div>',
                '<a href="' . esc_url( admin_url( 'options.php?page=custom-login-upgrades' ) ) . '">',
                '</a>'
            );
        }
    }

    /**
     * Box with latest plugins from Extendd.com for sidebar
     */
    public function sticky_admin_notice_social_links() {

        $content = '<ul class="social">';
        $content .= '<li><a href="https://www.facebook.com/FrostyMediaWP" target="_blank"><span class="dashicons dashicons-facebook"></span></a></li>';
        $content .= '<li><a href="https://twitter.com/Frosty_Media" target="_blank"><span class="dashicons dashicons-twitter"></span></a></li>';
        $content .= '<li><a href="https://plus.google.com/+FrostyMedia/" target="_blank"><span class="dashicons dashicons-googleplus"></span></a></li>';
        $content .= '<li><a href="http://eepurl.com/bbj0bD" target="_blank"><span class="dashicons dashicons-email"></span></a></li>';
        $content .= '</ul>';

        echo $content;
    }

    /**
     * Replace all square brackets with and underscore.
     *
     * @param string $input
     *
     * @return string
     */
    private function replace_bracket_underscore( $input ) {
        return preg_replace( '/[\[\]]/', '_', $input );
    }

    /**
     * Get the value of a settings field
     *
     * @param string $option settings field name
     * @param string $section the section name this field belongs to
     * @param string $default default text if it's not found
     *
     * @return string
     */
    function get_option( $option, $section, $default = '' ) {

        $options = get_option( $section, array() );

        if ( isset( $options[ $option ] ) ) {
            return $options[ $option ];
        }

        return $default;
    }

}