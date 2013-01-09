<?php 

/**
 * Load the feed from The Frosty network
 *
 * @since	12/3/12
 * @updated	12/3/12
 */
if ( !class_exists( 'the_frosty_dashboard' ) ) :
class the_frosty_dashboard {
	
	/**
	 * To infinity.
	 *
	 */
	function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}

	/**
	 * Add the dashboard widget
	 *
	 * @return 	void
	 */
	function add_dashboard_widget() {
		wp_add_dashboard_widget( 'thefrosty_dashboard', __( 'The Frosty Network <em>feeds</em>' ), array( $this, 'dashboard' ), array( $this, 'dashboard_control' ) );
	}
	
	/**
	 * Fetch RSS items from the feed.
	 *
	 * @param 	int    $num  Number of items to fetch.
	 * @param 	string $feed The feed to fetch.
	 * @return 	array|bool False on error, array of RSS items on success.
	 */
	public function fetch_rss_items( $num, $feed ) {
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
	 * Output CSS
	 *
	 * @return 	string
	 */
	function style() {
		?>
		<style>
        #frosty-dashboard .frosty-image {
            display: inline-block; 
            height: 25px;
            float: left; 
            width: 25px; 
            overflow: hidden
        }
        #frosty-dashboard .frosty-image span {
            background: url('<?php echo esc_url( plugin_dir_url( __FILE__ ) ) . 'Sprite.jpg'; ?>') 0 0 no-repeat;
            -webkit-background-size: 300px 25px;
            -moz-background-size: 300px 25px;
            -ms-background-size: 300px 25px;
            -o-background-size: 300px 25px;
            background-size: 300px 25px;
            display: inline-block;
            height: 25px; 
            width: 25px
        }
        #frosty-dashboard li { padding-left:30px }
        span.austinpassy { background-position: -25px 0 !important }
        span.frostywebdesigns { background-position: -50px 0 !important }
        span.jeanaarter { background-position: -75px 0 !important }
        span.wordcampla { background-position: -100px 0 !important }
        span.floatoholics { background-position: -125px 0 !important }
        span.thefrosty { background-position: -150px 0 !important }
        span.greatescapecabofishing { background-position: -175px 0 !important }
        span.eateryengine { background-position: -200px 0 !important }
        span.extendd { background-position: -225px 0 !important }
        </style>
		<?php
	}

	/**
	 * Print the dashboard widget
	 *
	 * @return 	string
	 */
	function dashboard( $sidebar_args ) {		
		$widget_options = get_option( 'the_frosty_dashboard_widget_options' );
		
		$item_count	= !empty( $widget_options['items'] ) ? $widget_options['items'] : 6;
		$rss_items  = $this->fetch_rss_items( $item_count, 'http://pipes.yahoo.com/pipes/pipe.run?_id=52c339c010550750e3e64d478b1c96ea&_render=rss' );
		
		$this->style();
		$content = '<ul id="frosty-dashboard">';
		if ( !$rss_items ) {
			$content .= '<li>' . __( 'Error fetching feed' ) . '</li>';
		} else {
			foreach ( $rss_items as $item ) {
				$title = esc_attr( strtolower( sanitize_title_with_dashes( htmlentities( $item->get_title() ) ) ) );
				
				$class = str_replace( array( 'http://', 'https://' ), '', $item->get_permalink() ); 
				$class = str_replace( array( '2010.', '2011.', '2012.', '2013.', '2014.', '2015.' ), '', $class );
				$class = str_replace( array( '.com/', '.net/', '.org/', '.la/', 'la.' ), ' ', $class );
				$class = str_replace( array( '2011/', '2012/', '2013/', '2014/' ), '', $class );
				$class = str_replace( array( '01/', '02/', '03/', '04/', '05/', '06/', '07/', '08/', '09/', '10/', '11/', '12/' ), '', $class );
				$class = str_replace( $title, '', $class );
				$class = str_replace( '/', '', $class );
				$class = str_replace( 'feedproxy.google', '', $class );
				$class = str_replace( '~r', '', $class );
				$class = str_replace( '~', ' ', $class );
				$class = trim( $class );
				list( $class, $therest ) = explode( ' ', $class );
				// Redundant, I know. Can you make a preg_replace for this?
				
				$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), null, 'display' ) );
				$content .= '<div class="frosty-image"><span class="' . strtolower( $class ) . '">&nbsp;</span></div>';
				$content .= '<li>';
				$content .= '<a class="rsswidget" href="' . $url . '">' . esc_html( $item->get_title() ) . '</a> ';
				$content .= '<span style="font-size:10px; color:#aaa;">' . esc_attr( $item->get_date('F, jS Y') ) . '</span>';
				$content .= '</li>';
			}
		}
		$content .= '</ul>';
		echo $content;			
	}
	
	/**
	 * Print the dashboard control widget
	 *
	 * @return 	string
	 */
	function dashboard_control() {
		if ( !$widget_options = get_option( 'the_frosty_dashboard_widget_options' ) )
			$widget_options = array();
	
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) && 'thefrosty_dashboard' == $_POST['widget_id'] ) {
			$items = stripslashes_deep( $_POST['thefrosty_dashboard_items'] );
			$widget_options['items'] = $items;
			update_option( 'the_frosty_dashboard_widget_options', $widget_options );
		}
		//print_r( $widget_options ); ?>
		<p><label for="thefrosty_dashboard_items"><?php _e('How many items would you like to display?'); ?></label>
        <select name="thefrosty_dashboard_items">
		<?php for ( $i = 3; $i <= 20; ++$i ) echo "<option value='$i' " . selected( $items, $i, false ) . ">$i</option>"; ?>
        </select></p>
        <?php
	}
	
}
$the_frosty_dashboard = new the_frosty_dashboard;
endif;

?>