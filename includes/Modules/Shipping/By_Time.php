<?php
/**
 * Shipping Module - Shipping by Time.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'By_Time' ) ) {

	/**
	 * Declare class `By_Time` extends to `Condition`.
	 */
	class By_Time extends Condition {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id         = 'shipping_by_time';
			$this->short_desc = __( 'Shipping Methods by Current Date/Time', 'all-in-one-wc' );
			$this->desc       = __( 'Set date and/or time to include/exclude for shipping methods to show up. (Free shipping available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set date and/or time to include/exclude for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-current-date-time';

			$this->condition_options = array(
				'time' => array(
					'title' => __( 'Current Date/Time', 'all-in-one-wc' ),
					'desc'  => '<br>' . sprintf( __( 'Current time: %s.', 'all-in-one-wc' ), '<code>' . current_time( 'Y-m-d H:i:s' ) . '</code>' ) . '<br>' .
						sprintf( __( 'Time <em>from</em> and time <em>to</em> must be separated with %s symbol.', 'all-in-one-wc' ), '<code>~</code>' ) . ' ' .
						sprintf( __( 'Each time input must be set in format that is parsable by PHP %s function.', 'all-in-one-wc' ),
							'<a href="http://php.net/manual/en/function.strtotime.php" target="_blank"><code>strtotime()</code></a>' ) . ' ' .
						sprintf( __( 'Valid time input examples are: %s', 'all-in-one-wc' ), '<ul><li><code>' . implode( '</code></li><li><code>', array(
								'this week Thursday 4:30pm ~ this week Friday 4:30pm',
								'this year September 1 ~ this year September 30',
							) ) . '</code></li></ul>' ),
					'type'  => 'text',
					'class' => '',
					'css'   => 'width:100%',
				),
			);

			parent::__construct();

		}

		/**
		 * Parse time.
		 *
		 * @param string $value Value.
		 * @return string
		 */
		function parse_time( $value ) {
			$value = explode( '~', $value );
			if ( 2 != count( $value ) ) {
				return false;
			}
			if ( false === ( $time_from = strtotime( $value[0] ) ) ) {
				return false;
			}
			if ( false === ( $time_to   = strtotime( $value[1] ) ) ) {
				return false;
			}
			return array( 'time_from' => $time_from, 'time_to' => $time_to );
		}

		/**
		 * Condition check
		 *
		 * @param string $options_id Option ID.
		 * @param string $values Values.
		 * @param bool   $include_or_exclude Include OR exclude.
		 * @param string $package Package.
		 * @return bool
		 */
		function check( $options_id, $values, $include_or_exclude, $package ) {
			switch( $options_id ) {
				case 'time':
					if ( $parsed_time = $this->parse_time( $values ) ) {
						$current_time = (int) current_time( 'timestamp' );
						return ( $current_time >= $parsed_time['time_from'] && $current_time <= $parsed_time['time_to'] );
					}
					return ( 'include' == $include_or_exclude ); // not parsable time input - leaving shipping method enabled
			}
		}

		/**
		 * Get condition options.
		 *
		 * @param string $options_id Option ID.
		 * @return string
		 */
		function get_condition_options( $options_id ) {
			switch( $options_id ) {
				case 'time':
					return '';
			}
		}

		/**
		 * Get extra option desc.
		 *
		 * @param string $option_id Option ID.
		 * @return mixed
		 */
		function get_extra_option_desc( $option_id ) {
			$values = aiow_option( $option_id, '' );
			if ( ! empty( $values ) ) {
				if ( $parsed_time = $this->parse_time( $values ) ) {
					return '. ' . sprintf( __( 'According to current time, your time input will be parsed as: from %s to %s.', 'all-in-one-wc' ),
						'<code>' . date( 'Y-m-d H:i:s', $parsed_time['time_from'] ) . '</code>', '<code>' . date( 'Y-m-d H:i:s', $parsed_time['time_to'] ) . '</code>' );
				} else {
					return '. <strong>' . sprintf( __( 'Error: %s', 'all-in-one-wc' ), __( 'Time input is not parsable!', 'all-in-one-wc' ) ) . '</strong>';
				}
			}
			return '';
		}
	}
}
