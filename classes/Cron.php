<?php

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RCPM\Cron Class
 *
 * This class handles scheduled events
 *
 * @since 1.0.0
 */
class Cron {

	/**
	 * Get things going
	 *
	 * @since 1.0.0
	 * @see RCPM\Cron::weekly_events()
	 */
	public static function init() {
		add_filter( 'cron_schedules', array( __CLASS__, 'add_schedules' ) );
		add_action( 'wp', array( __CLASS__, 'schedule_events' ) );
	}

	/**
	 * Registers new cron schedules
	 *
	 * @since 1.0.0
	 *
	 * @param array $schedules
	 *
	 * @return array
	 */
	public static function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 7 * DAY_IN_SECONDS,
			'display'  => __( 'Once Weekly', 'recipe-manager' ),
		);

		return $schedules;
	}

	/**
	 * Schedules our events
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function schedule_events() {
		self::weekly_events();
		self::daily_events();
	}

	/**
	 * Schedule weekly events
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private static function weekly_events() {
		if ( ! wp_next_scheduled( 'rcpm_weekly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'rcpm_weekly_scheduled_events' );
		}
	}

	/**
	 * Schedule daily events
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private static function daily_events() {
		if ( ! wp_next_scheduled( 'rcpm_daily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'rcpm_daily_scheduled_events' );
		}
	}

}
