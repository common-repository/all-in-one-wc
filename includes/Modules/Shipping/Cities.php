<?php
/**
 * Shipping Module - Shipping by Cities.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Cities' ) ) {

	/**
	 * Declare class `Cities` extends to `Condition`.
	 */
	class Cities extends Condition {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_by_cities';
			$this->short_desc = __( 'Shipping Methods by City or Postcode', 'all-in-one-wc' );
			$this->desc       = __( 'Set shipping cities or postcodes to include/exclude for shipping methods to show up. (Free shipping available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set shipping cities or postcodes to include/exclude for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-cities';

			$this->condition_options = array(
				'cities' => array(
					'title' => __( 'Cities', 'all-in-one-wc' ),
					'desc'  => __( 'Otherwise enter cities one per line.', 'all-in-one-wc' ),
					'type'  => 'textarea',
					'class' => '',
					'css'   => 'height:200px;',
				),
				'postcodes' => array(
					'title' => __( 'Postcodes', 'all-in-one-wc' ),
					'desc'  => __( 'Otherwise enter postcodes one per line.', 'all-in-one-wc' ) . '<br>' .
						'<em>' . __( 'Postcodes containing wildcards (e.g. CB23*) and fully numeric ranges (e.g. <code>90210...99000</code>) are also supported.', 'woocommerce' ) . '</em>',
					'type'  => 'textarea',
					'class' => '',
					'css'   => 'height:200px;',
				),
			);

			parent::__construct();

		}

		/**
		 * Condition check.
		 *
		 * @param string $options_id Option ID.
		 * @param string $values Values.
		 * @param bool   $include_or_exclude Include OR exclude.
		 * @param string $package Package.
		 * @return string
		 */
		function check( $options_id, $values, $include_or_exclude, $package ) {
			switch ( $options_id ) {
				case 'cities':
					$customer_city = strtoupper( isset( $_REQUEST['s_city'] ) ? $_REQUEST['s_city'] : ( isset ( $_REQUEST['calc_shipping_city'] ) ? $_REQUEST['calc_shipping_city'] : ( ! empty( $user_city = WC()->customer->get_shipping_city() ) ? $user_city : WC()->countries->get_base_city() ) ) );
					$values        = array_map( 'strtoupper', array_map( 'trim', explode( PHP_EOL, $values ) ) );
					return in_array( $customer_city, $values );
				case 'postcodes':
					$customer_postcode = strtoupper( isset( $_REQUEST['s_postcode'] ) ? $_REQUEST['s_postcode'] : ( ! empty( $customer_shipping_postcode = WC()->customer->get_shipping_postcode() ) ? $customer_shipping_postcode : WC()->countries->get_base_postcode() ) );
					$postcodes         = array_map( 'strtoupper', array_map( 'trim', explode( PHP_EOL, $values ) ) );
					return aiow_check_postcode( $customer_postcode, $postcodes );
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
				case 'cities':
					return '';
				case 'postcodes':
					return '';
			}
		}
	}
}
