<?php
/**
 * @package     CustomLogin
 * @subpackage  Admin/Classes/Dashboard
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WordPress dashboard
 *
 * @access public
 * @since  3.1
 * @return void
 */
class CL_Dashboard {

    /** Singleton *************************************************************/
    private static $instance;

    private $id;

    private static $headers = array();
    private static $scripts = array();

    const FEED_URL = 'https://frosty.media/wp-json/wp/v2/posts';

    /**
     * Main Instance
     *
     * @staticvar 	array 	$instance
     * @return 		CL_Dashboard The one true instance
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
            self::$instance->id = sprintf( '%s-dashboard', CUSTOM_LOGIN_DIRNAME );
            self::$instance->actions();
        }
        return self::$instance;
    }

    private function actions() {

        if (!is_blog_admin()) {
            return;
        }

        add_action( 'wp_dashboard_setup',			array( $this, 'add_dashboard_widget' ) );
        add_action( 'admin_enqueue_scripts',		array( $this, 'inline_scripts' ) );
        add_action( 'admin_footer',					array( $this, 'admin_footer' ) );
    }

    /**
     * Check if the dashboard widget is allowed.
     *
     * @access private
     * @return bool
     */
    private function dashboard_allowed() {
        $dashboard = CL_Common::get_option( 'dashboard_widget', 'general', 'off' );

        if ( 'on' === $dashboard )
            return true;

        return false;
    }

    /**
     * Add Dashboard widget
     */
    public function add_dashboard_widget() {

        if (!$this->dashboard_allowed()) {
            return;
        }

        wp_add_dashboard_widget(
            $this->id,
            __( 'Frosty Media', CUSTOM_LOGIN_DIRNAME ),
            array( $this, 'widget' )
        );
    }

    public function admin_footer() {
        if ( $this->dashboard_allowed() ) {
            $this->CSS( false );
        } else {
            $this->jQuery( false );
        }
    }

    /**
     * @param int $count
     * @param string $feed
     * @return array|bool
     */
    private function get_feed( $count = 1, $feed = self::FEED_URL ) {
        return CL_Common::get_posts_via_rest( $feed, $count );
    }

    private function get_feed_url() {
        $rss_items = $this->get_feed();
        if (isset($rss_items[0])) {
            return esc_url(
                add_query_arg(
                    [
                        'utm_medium' => 'wpadmin_dashboard',
                        'utm_term' => 'newsitem',
                        'utm_campaign' => CUSTOM_LOGIN_DIRNAME,
                    ],
                    array_shift($rss_items)->guid->rendered
                )
            );
        }

        return esc_url( self::FEED_URL );
    }

    private function get_feed_title() {

        $rss_items = $this->get_feed();

        return empty( $rss_items ) ? 'Unknown' : esc_html( array_shift($rss_items)->title->rendered );
    }

    /**
     * Dashboard widget
     */
    public function widget() {
        // FEED
        $posts = $this->get_feed(2);

        $content  = '<div class="rss-widget">';
        $content .= '<ul>';

        if ( empty($posts) ) {
            $content .= '<li>' . __( 'Error fetching feed', CUSTOM_LOGIN_DIRNAME ) . '</li>';
        } else {
            $count = 1;
            foreach ( $posts as $item ) {
                $content .= '<li>';
                $content .= '<a class="rsswidget" href="' . esc_url( add_query_arg( array( 'utm_medium' => 'wpadmin_dashboard', 'utm_term' => 'newsitem', 'utm_campaign' => CUSTOM_LOGIN_DIRNAME ), $item->guid->rendered ) ) . '">' .	esc_html( $item->title->rendered ) . '</a>';
                $content .= $count === 1 ? '&nbsp;&nbsp;&nbsp;<span class="rss-date">' . date_i18n( get_option( 'date_format' ), strtotime( $item->date ) ) . '</span>' : '';
                $content .= $count === 1 ? '<div class="rssSummary">' . strip_tags( wp_trim_words( $item->content->rendered, 28 ) ) . '</div>' : '';
                $content .= '</li>';
                $count++;
            }
        }
        $content .= '</ul>';
        $content .= '</div>';


        // Plugins (Taxonomy: plugin_tag; Term: custom-login-extension; Term ID: 29)
        $extensions = $this->get_feed(
            10,
            sprintf('%s?plugin_tag=29', str_replace('/posts', '/extensions', self::FEED_URL))
        );

        if (!empty($extensions)) {
            $content .= '<div class="rss-widget">';
            $content .= '<ul>';
            $content .= '<li><strong>' . __('Custom Login Extensions:', CUSTOM_LOGIN_DIRNAME) . '</strong></li>';
            $content .= '<li>';
            $item = $extensions[array_rand($extensions)];
            $content .= '<a class="rsswidget" href="' . esc_url(add_query_arg([
                    'utm_medium' => 'wpadmin_dashboard',
                    'utm_term' => 'newsitem',
                    'utm_campaign' => CUSTOM_LOGIN_DIRNAME,
                ], $item->guid->rendered)) . '">' . esc_html($item->title->rendered) . '</a>';
//                $content .= '<div class="rssSummary">' . strip_tags( wp_trim_words( $item->content->rendered, 10 ) ) . '</div>';
            $content .= '</li>';
            $content .= '</ul>';
            $content .= '</div>';
        }

        $content .= '<div class="rss-widget">';
        $content .= '<ul class="social">';
        $content .= '<li>';
        $content .= '<a href="https://twitter.com/Frosty_Media"><span class="dashicons dashicons-twitter"></span>/Frosty_Media</a> | ';
        $content .= '<a href="https://twitter.com/TheFrosty"><span class="dashicons dashicons-twitter"></span>/TheFrosty</a>';
        $content .= '</li>';
        $content .= '</ul>';

        $content .= '</div>';

        echo $content;
    }

    /**
     * Generate the custom CSS/JS.
     *
     */
    public function inline_scripts() {

        if ( isset( $_GET[ $this->id ] ) && intval( $_GET[ $this->id ] ) === 1 ) {

            if ( isset( $_GET['type'] ) && $_GET['type'] === 'css' ) {

                if ( !headers_sent() ) {
                    header("content-type:text/css");
                }
                ob_start();
                $this->CSS();
                if ( ob_get_level() ) echo ob_get_clean();
                die;
            } elseif ( isset( $_GET['type'] ) && $_GET['type'] === 'js' ) {

                if ( !headers_sent() ) {
                    header("content-type:application/x-javascript");
                }
                ob_start();
                $this->jQuery();
                if ( ob_get_level() ) echo ob_get_clean();
                die;
            }
        }
    }

    public function clean_ob_contents( $contents ) {
        return str_replace( $contents, '', $contents );
    }

    /**
     * Helper function to return the proper query arg.
     */
    private function add_query_arg( $type = 'js' ) {
        $url = add_query_arg(
            array(
                $this->id	=> '1',
                'type'		=> $type
            ),
            trailingslashit( admin_url() )
        );
        return esc_url( $url );
    }

    /**
     * Create the CSS.
     *
     * @param 	bool $remove_wrapper
     */
    private function CSS( $remove_wrapper = true ) {
        if ( !$remove_wrapper ) { ?>
            <style>
        <?php }
        #<?php echo $this->id; ?> .inside {
        margin: 0;
        padding: 0;
    }
    #<?php echo $this->id; ?> .rss-widget {
                                  border-bottom: 1px solid #eee;
                                  font-size: 13px;
                                  padding: 8px 12px 10px;
                              }
    <?php if ( !$remove_wrapper ) { ?>
        </style>
    <?php }
    }

    /**
     * Create the jQuery.
     *
     * @param 	bool $remove_wrapper
     */
    private function jQuery( $remove_wrapper = true ) {
    if ( !$remove_wrapper ) { ?>
        <script>
            <?php } ?>
            jQuery(document).ready(function($) {
                if ( !$('div.wordpress-news:visible') ) {
                    return;
                }

                let CL_Timeout = 200

                if ( !$('#dashboard_primary .rss-widget').eq(1).length ) {
                    CL_Timeout = 2500;
                }

                setTimeout( function() {
                    $('#dashboard_primary .rss-widget:eq(1) ul').append('<li><a class="rsswidget" target="_blank" href="<?php echo $this->get_feed_url(); ?>">Frosty Media: <?php echo $this->get_feed_title(); ?></a></li>');
                }, CL_Timeout );

            });
            <?php if ( !$remove_wrapper ) { ?>
        </script>
    <?php }
    }

}

// Only load on the WordPress Dashboard (index.php) page.
add_action( 'load-index.php', array( 'CL_Dashboard', 'instance' ), 99 );
