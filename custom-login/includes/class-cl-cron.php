<?php
/**
 * @package     CustomLogin
 * @subpackage  Classes/CL_Cron
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CL_Cron Class
 *
 * This class handles scheduled events
 *
 */
class CL_Cron {
	
	/**
	 * Get things going
	 *
	 * @since 1.6
	 * @see CL_Cron::weekly_events()
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'wp',             array( $this, 'schedule_events' ) );
	}

	/**
	 * Registers new cron schedules
	 *
	 * @since 1.6
	 *
	 * @param array $schedules
	 * @return array
	 */
	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', CUSTOM_LOGIN_DIRNAME )
		);

		return $schedules;
	}

	/**
	 * Schedules our events
	 *
	 * @access public
	 * @since 1.6
	 * @return void
	 */
	public function schedule_events() {
		$this->weekly_events();
		$this->daily_events();
	}

	/**
	 * Schedule weekly events
	 *
	 * @access private
	 * @since 1.6
	 * @return void
	 */
	private function weekly_events() {
		if ( ! wp_next_scheduled( 'custom_login_weekly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'custom_login_weekly_scheduled_events' );
		}
	}

	/**
	 * Schedule daily events
	 *
	 * @access private
	 * @since 1.6
	 * @return void
	 */
	private function daily_events() {
		if ( ! wp_next_scheduled( 'custom_login_daily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'custom_login_daily_scheduled_events' );
		}
	}

}
$cl_cron = new CL_Cron;