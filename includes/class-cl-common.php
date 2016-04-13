<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Common
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class CL_Common {

    /**
     * Return the RSS feed object.
     *
     * @param string $feed The feed to fetch.
     *
     * @return object
     */
    public static function fetch_feed( $feed ) {

        if ( !function_exists( 'fetch_feed' ) ) {
            include_once( ABSPATH . WPINC . '/feed.php' );
        }

        return fetch_feed( $feed );
    }

    /**
     * Fetch RSS items from the feed.
     *
     * @param 	int    $num  Number of items to fetch.
     * @param 	string $feed The feed to fetch.
     * @return 	array|bool False on error, array of RSS items on success.
     */
    public static function fetch_rss_items( $num, $feed ) {

        $rss = self::fetch_feed( $feed );
        $maxitems = 0;

        if ( !is_wp_error( $rss ) ) { // Checks that the object is created correctly

            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity( $num );

            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items( 0, $maxitems );

        }
        else {
            return false;
        }

        // If the feed was erroneous
        if ( !$rss_items || $maxitems == 0 ) {
            $md5 = md5( $feed );
            delete_transient( 'feed_' . $md5 );
            delete_transient( 'feed_mod_' . $md5 );
            $rss       = self::fetch_feed( $feed );
            $rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
        }

        return $rss_items;
    }

    /**
     * Helper function to return the data URI.
     *
     * @return string
     */
    public static function get_data_uri( $_image, $mime = '' ) {

        $image  = trailingslashit( CUSTOM_LOGIN_URL );
        $image .= $_image;

        $data = file_exists( $image ) ? base64_encode( file_get_contents( $image ) ) : '';

        return !empty( $data ) ? 'data:image/' . $mime . ';base64,' . $data : '';
    }

    /**
     * Get's the cached transient key.
     *
     * @return string
     */
    public static function get_transient_key( $input ) {

        $len = is_multisite() ? 40 : 45;
        $key = 'custom_login_';
        $key = $key . substr( md5( $input ), 0, $len - strlen( $key ) );

        return $key;
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $subsection the section name this field belongs to
     * @param string  $default default text if it's not found
     *
     * @return string
     */
    public static function get_option( $option, $subsection = '', $default = '' ) {

        $section = CUSTOM_LOGIN_OPTION . '_' . $subsection;
        $setting = get_option( $section, array() );

        if ( isset( $setting[$option] ) ) {
            return $setting[$option];
        }

        return $default;
    }

    /**
     * Get all values of a settings section
     *
     * @param string  $subsection the section name this field belongs to
     *
     * @return array
     */
    public static function get_options( $subsection = 'design' ) {

        $section  = CUSTOM_LOGIN_OPTION . '_' . $subsection;
        $settings = get_option( $section, array() );

        return $settings;
    }

    /**
     * Helper function to make remote calls
     *
     * @since		3.0.0
     * @updated	3.0.8
     */
    public static function wp_remote_get( $url = false, $transient_key, $expiration = null, $user_agent = 'WordPress' ) {

        if ( !$url ) return false;

        if ( 'WordPress' == $user_agent ) {
            global $wp_version;
            $_version = $wp_version;
        }
        else {
            $_version = CUSTOM_LOGIN_VERSION;
        }

        $expiration = null !== $expiration ? $expiration : WEEK_IN_SECONDS;

        #	delete_transient( $transient_key );
        if ( false === ( $json = get_transient( $transient_key ) ) ) {

            $response = wp_remote_get(
                esc_url( $url ),
                array(
                    'timeout'		=> apply_filters( 'cl_wp_remote_get_timeout', (int) 15 ),
                    'sslverify'		=> false,
                    'user-agent'	=> $user_agent . '/' . $_version . '; ' . get_bloginfo( 'url' ),
                )
            );

            if ( !is_wp_error( $response ) ) {

                if ( isset( $response['body'] ) && strlen( $response['body'] ) > 0 ) {

                    $json = json_decode( wp_remote_retrieve_body( $response ) );

                    // Discount, double check?
                    if ( is_wp_error( $json ) )
                        return false;

                    // Cache the results for '$expiration'
                    set_transient( $transient_key, $json, $expiration );

                    // Return the data
                    return $json;
                }
            }
            else {
                return false; // Error, lets return!
            }
        }

        return $json;
    }

    /**
     * Helper function check if we're on our settings page.
     *
     * @since		3.0.9
     */
    public static function is_settings_page( $page = '' ) {

        $return = true;
        $screen = get_current_screen();

        if ( null !== $screen ) {

            if ( $screen->id != ( CUSTOMLOGIN()->menu_page ) )
                $return = false;
        }
        else {
            global $pagenow;

            if ( 'options-general.php' != $pagenow )
                $return = false;

            if ( !isset( $_GET['page'] ) || CUSTOM_LOGIN_DIRNAME != $_GET['page'] )
                $return = false;
        }

        return $return;
    }

}
