<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_WP_Login
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CL_WP_Login {

    /** Singleton *************************************************************/
    private static $instance;

    /**
     * Main Instance
     *
     * @staticvar    array    $instance
     * @return self The one true instance
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
            self::$instance->init();
        }

        return self::$instance;
    }

    private function init() {

        if ( 'off' === CL_Common::get_option( 'active', 'general', 'off' ) ) {
            return;
        }

        $this->actions();
        $this->filters();
    }

    private function actions() {

        add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
        add_action( 'login_footer', array( $this, 'login_footer_html' ), 8 );
        add_action( 'login_footer', array( $this, 'login_footer_jquery' ), 19 );

        add_action( 'init', array( $this, 'login_remove_scripts' ) );
        add_action( 'login_head', array( $this, 'login_head' ), 10 );
        add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
        add_filter( 'login_headertitle', array( $this, 'login_headertitle' ) );
    }

    private function filters() {

        #	add_filter( 'auth_cookie_expiration',			array( $this, 'auth_cookie_expiration' ), 99, 3 ); // @removed 3.1
        #	add_filter( 'allow_password_reset',				array( $this, 'allow_password_reset' ) ); // @removed 3.1
        add_filter( 'gettext', array( $this, 'remove_lostpassword_text' ), 20, 2 );
    }

    /**
     *************************************************************
     ****************  ACTIONS  **********************************
     *************************************************************
     */

    /**
     * Enqueue additional scripts.
     *
     * @since        2.0
     * @updated        3.2
     */
    function login_enqueue_scripts() {
        global $cl_css_atts;

        $cl_css_atts = array(
            'version'   => CUSTOM_LOGIN_VERSION,
            'trans_key' => CL_Common::get_transient_key( 'style' ),
        );
        $cl_css_atts = wp_parse_args( CL_Common::get_options( 'design' ), $cl_css_atts );

        ob_start();
        echo "<style type=\"text/css\">\n";
        CL_Templates::get_template_part( 'wp-login', 'style' );
        echo "\n</style>\n";
        echo ob_get_clean();

        /**
         * Animate.css
         * @ref        https://github.com/daneden/animate.css/blob/master/animate.min.css
         */
        $animate_css = CL_Common::get_option( 'animate.css', 'design', 'off' );
        if ( 'on' == $animate_css ) {
            wp_enqueue_style( 'animate.css', plugins_url( 'css/animate.min.css', CUSTOM_LOGIN_FILE ), array( 'login' ), '08112014', 'screen' );
        }

        /* Custom jQuery */
        $jquery = CL_Common::get_option( 'custom_jquery', 'design', '' );
        if ( '' != $jquery ) {
            wp_enqueue_script( array( 'jquery' ) );
        }
    }

    /**
     * If there is custom HTML set in the settings echo it to the
     * 'login_footer' hook in wp-login.php.
     *
     * @return        string|void
     */
    public function login_footer_html() {

        $custom_html = CL_Common::get_option( 'custom_html', 'design', false );

        if ( $custom_html ) {
            $html = wp_kses_post( $custom_html );
            $html .= "\n";

            echo $html;
        }
    }

    /**
     * Database access to the scripts and styles.
     *
     * @since        2.1
     * @return        string|void
     */
    public function login_footer_jquery() {

        $jquery = CL_Common::get_option( 'custom_jquery', 'design', '' );

        if ( '' != $jquery ) {

            global $cl_js_atts;

            $cl_js_atts = array(
                'version'   => CUSTOM_LOGIN_VERSION,
                'trans_key' => CL_Common::get_transient_key( 'script' ),
            );
            $cl_js_atts = wp_parse_args( CL_Common::get_options( 'design' ), $cl_js_atts );

            foreach ( $cl_js_atts as $atts => $value ) {
                if ( 'custom_jquery' !== $atts && 'version' !== $atts && 'trans_key' !== $atts ) {
                    unset( $cl_js_atts[ $atts ] );
                }
            }

            ob_start();
            echo "<script type=\"text/javascript\">\n";
            CL_Templates::get_template_part( 'wp-login', 'script' );
            echo "\n</script>\n";
            echo ob_get_clean();
        }
    }

    /**
     * Finds the global page for the wp-login.php. When on the page
     * remove default stylesheets so we can add our own.
     *
     * @return    void
     */
    function login_remove_scripts() {
        global $pagenow;

        if ( 'wp-login.php' == $pagenow ) {

            $suffix = is_rtl() ? '-rtl' : '';
            $suffix .= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; // Don't have minified version in place.

            /**
             * User reports on messed up checkboxes
             *
             * Probobly easier to use WordPress login CSS
             *
             * wp_deregister_style( array( 'login' ) );
             *
             * wp_enqueue_style( 'forms', get_admin_url( get_current_blog_id(), "css/forms{$suffix}.css", 'admin' ), null, CUSTOM_LOGIN_VERSION, 'screen' );
             * wp_enqueue_style( 'l10n', get_admin_url( get_current_blog_id(), "css/l10n{$suffix}.css", 'admin' ), null, CUSTOM_LOGIN_VERSION, 'screen' );
             * wp_register_style( 'login', plugins_url( "css/login/login{$suffix}.css", CUSTOM_LOGIN_FILE ), array( 'buttons' ), CUSTOM_LOGIN_VERSION, 'all' );
             */

            if ( 'on' === CL_Common::get_option( 'remove_login_css', 'general' ) ) {
                add_filter( 'wp_admin_css', '__return_false' );
                wp_deregister_style( array( 'login' ) );
            }
        }
    }

    /**
     * Actions hooked into login_head
     *
     */
    public function login_head() {
        global $cl_css_atts;

        $cl_css_atts = array(
            'version'   => CUSTOM_LOGIN_VERSION,
            'trans_key' => CL_Common::get_transient_key( 'style' ),
        );
        $cl_css_atts = wp_parse_args( CL_Common::get_options( 'design' ), $cl_css_atts );

        ob_start();
        echo "<style type=\"text/css\">\n";
        CL_Templates::get_template_part( 'wp-login', 'style' );
        echo "\n</style>\n";
        echo ob_get_clean();

        if ( 'on' === CL_Common::get_option( 'wp_shake_js', 'general' ) ) {
            remove_action( 'login_head', 'wp_shake_js', 12 );
        }
    }

    /**
     * Replace the default link to your URL
     */
    public function login_headerurl() {

        if ( ! is_multisite() ) {
            return home_url();
        }
    }

    /**
     * Replace the default title to your description
     */
    public function login_headertitle() {

        if ( ! is_multisite() ) {
            return get_bloginfo( 'description' );
        }
    }

    /**
     *************************************************************
     ****************  FILTERS  **********************************
     *************************************************************
     */

    /**
     * Allow password reset.
     *
     * @added        3.0.5
     * @updated        3.0.8
     * @disabled    3.1.0
     * @ref            https://wordpress.org/plugins/configure-login-timeout/
     * @removed        3.1
     */
    public function auth_cookie_expiration( $seconds, $user_id, $remember ) {

        $expire_in = 0;

        if ( $remember ) {
            $expire_in = (int) CL_Common::get_option( 'auth_timeout_remember', 'general', 14 * DAY_IN_SECONDS );
            if ( $expire_in <= 0 ) {
                $expire_in = 14 * DAY_IN_SECONDS;
            }
        } else {
            $expire_in = (int) CL_Common::get_option( 'auth_timeout', 'general', 2 * DAY_IN_SECONDS );
            if ( $expire_in <= 0 ) {
                $expire_in = 2 * DAY_IN_SECONDS;
            }
        }

        // check for Year 2038 problem - http://en.wikipedia.org/wiki/Year_2038_problem
        if ( PHP_INT_MAX - time() < $expire_in ) {
            $expire_in = PHP_INT_MAX - time() - 5;
        }

        return $expire_in;
    }

    /**
     * Allow password reset.
     *
     * 'on' equals don't allow. :/
     *
     * @updated    3.0.5
     * @removed    3.1
     */
    public function allow_password_reset( $user_id ) {
        if ( 'on' === CL_Common::get_option( 'allow_password_reset', 'general', 'off' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Remove the "Lost your password?" text.
     */
    public function remove_lostpassword_text( $translated_text, $untranslated_text ) {
        global $pagenow;

        if ( 'wp-login.php' == $pagenow ) {

            if ( 'off' !== CL_Common::get_option( 'lostpassword_text', 'general' ) ) {
                //make the changes to the text
                switch ( $untranslated_text ) {

                    case 'Lost your password?':
                        $translated_text = '';
                        break;
                }
            }
        }

        return $translated_text;
    }

}

add_action( CUSTOM_LOGIN_OPTION . '_actions', array( 'CL_WP_Login', 'instance' ) );