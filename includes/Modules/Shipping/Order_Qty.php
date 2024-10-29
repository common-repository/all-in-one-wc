<?php
/**
 * Shipping Module - Shipping Methods by Min/Max Order Quantity
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Order_Qty' ) ) {

	/**
	 * Declare class `Order_Qty` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Order_Qty extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_by_order_qty';
			$this->short_desc = __( 'Shipping Methods by Min/Max Order Quantity', 'all-in-one-wc' );
			$this->desc       = __( 'Set minimum and/or maximum order quantity for shipping methods to show up (Local pickup available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set minimum and/or maximum order quantity for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-min-max-order-quantity';
			parent::__construct();

			if ( $this->is_enabled() ) {
				$this->use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_by_order_qty_use_shipping_instance', 'no' ) );
				$min_option_name = 'aiow_shipping_by_order_qty_min';
				$max_option_name = 'aiow_shipping_by_order_qty_max';
				if ( $this->use_shipping_instances ) {
					$min_option_name .= '_instance';
					$max_option_name .= '_instance';
				}
				$this->min_qty = aiow_option( $min_option_name, array() );
				$this->max_qty = aiow_option( $max_option_name, array() );
				add_filter( 'woocommerce_package_rates', array( $this, 'available_shipping_methods' ), PHP_INT_MAX, 2 );
			}
		}

		/**
		 * Get min max QTY.
		 *
		 * @param object $rate Rate.
		 * @param string $min_or_max Min OR max.
		 * @return bool
		 */
		function get_min_max_qty( $rate, $min_or_max ) {
			$key = ( $this->use_shipping_instances ? $rate->instance_id : $rate->method_id );
			switch ( $min_or_max ) {
				case 'min':
					return ( isset( $this->min_qty[ $key ] ) ? $this->min_qty[ $key ] : 0 );
				case 'max':
					return ( isset( $this->max_qty[ $key ] ) ? $this->max_qty[ $key ] : 0 );
			}
		}

		/**
		 * Available shipping methods.
		 *
		 * @param array  $rates Rates.
		 * @param string $package Package.
		 * @return array
		 */
		function available_shipping_methods( $rates, $package ) {
			if ( ! isset( WC()->cart ) || WC()->cart->is_empty() ) {
				return $rates;
			}
			$total_qty = WC()->cart->get_cart_contents_count();
			foreach ( $rates as $rate_key => $rate ) {
				if ( 0 != ( $min = $this->get_min_max_qty( $rate, 'min' ) ) && $total_qty < $min ) {
					unset( $rates[ $rate_key ] );
				} elseif ( 0 != ( $max = $this->get_min_max_qty( $rate, 'max' ) ) && $total_qty > $max ) {
					unset( $rates[ $rate_key ] );
				}
			}
			return $rates;
		}
	}
}
