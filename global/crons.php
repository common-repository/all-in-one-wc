<?php
/**
 * Functions - Crons
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_crons_get_all_intervals' ) ) {
	/**
	 * Crons get all intervals.
	 *
	 * @param string $action Action.
	 * @param array  $skip_intervals Skip intervals.
	 * @return array
	 */
	function aiow_crons_get_all_intervals( $action = '', $skip_intervals = array() ) {
		if ( '' === $action ) {
			$action = __( 'Update', 'all-in-one-wc' );
		}
		$return = array(
			'minutely'   => sprintf( __( '%s every minute', 'all-in-one-wc' ), $action ),
			'minute_5'   => sprintf( __( '%s every 5 minutes', 'all-in-one-wc' ), $action ),
			'minute_15'  => sprintf( __( '%s every 15 minutes', 'all-in-one-wc' ), $action ),
			'minute_30'  => sprintf( __( '%s every 30 minutes', 'all-in-one-wc' ), $action ),
			'hourly'     => sprintf( __( '%s hourly', 'all-in-one-wc' ), $action ),
			'twicedaily' => sprintf( __( '%s twice daily', 'all-in-one-wc' ), $action ),
			'daily'      => sprintf( __( '%s daily', 'all-in-one-wc' ), $action ),
			'weekly'     => sprintf( __( '%s weekly', 'all-in-one-wc' ), $action ),
		);
		if ( ! empty( $skip_intervals ) ) {
			foreach ( $skip_intervals as $skip_interval ) {
				unset( $return[ $skip_interval ] );
			}
		}
		return $return;
	}
}

if ( ! function_exists( 'aiow_crons_schedule_the_events' ) ) {
	/**
	 * Crons schedule the events.
	 *
	 * @param string $event_hook Event hook.
	 * @param string $selected_interval Selected interval.
	 */
	function aiow_crons_schedule_the_events( $event_hook, $selected_interval ) {
		$intervals = array_keys( aiow_crons_get_all_intervals() );
		foreach ( $intervals as $interval ) {
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( $selected_interval === $interval ) {
				update_option( $event_hook . '_time', $event_timestamp );
			}
			if ( ! $event_timestamp && $selected_interval === $interval ) {
				wp_schedule_event( time(), $selected_interval, $event_hook, array( $selected_interval ) );
			} elseif ( $event_timestamp && $selected_interval !== $interval ) {
				wp_unschedule_event( $event_timestamp, $event_hook, array( $interval ) );
			}
		}
	}
}

if ( ! function_exists( 'aiow_crons_get_next_event_time_message' ) ) {
	/**
	 * Crons get next event time message.
	 *
	 * @param string $time_option_name Option name.
	 * @return null
	 */
	function aiow_crons_get_next_event_time_message( $time_option_name ) {
		if ( '' != aiow_option( $time_option_name, '' ) ) {
			$scheduled_time_diff = aiow_option( $time_option_name, '' ) - time();
			if ( $scheduled_time_diff > 60 ) {
				return '<br><em>' . sprintf( __( '%s till next run.', 'all-in-one-wc' ), human_time_diff( 0, $scheduled_time_diff ) ) . '</em>';
			} elseif ( $scheduled_time_diff > 0 ) {
				return '<br><em>' . sprintf( __( '%s seconds till next run.', 'all-in-one-wc' ), $scheduled_time_diff ) . '</em>';
			}
		}
		return '';
	}
}

if ( ! function_exists( 'aiow_crons_add_custom_intervals' ) ) {
	/**
	 * Crons add custom intervals.
	 *
	 * @param array $schedules Cron schedules.
	 * @return array
	 */
	function aiow_crons_add_custom_intervals( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once weekly', 'all-in-one-wc' )
		);
		$schedules['minute_30'] = array(
			'interval' => 1800,
			'display'  => __( 'Once every 30 minutes', 'all-in-one-wc' )
		);
		$schedules['minute_15'] = array(
			'interval' => 900,
			'display'  => __( 'Once every 15 minutes', 'all-in-one-wc' )
		);
		$schedules['minute_5'] = array(
			'interval' => 300,
			'display'  => __( 'Once every 5 minutes', 'all-in-one-wc' )
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display'  => __( 'Once a minute', 'all-in-one-wc' )
		);
		return $schedules;
	}
}
