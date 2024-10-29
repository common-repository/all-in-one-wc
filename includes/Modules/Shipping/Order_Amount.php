<?php
/**
 * Shipping Module - Shipping Methods by Min/Max Order Amount.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Order_Amount' ) ) {

	/**
	 * Declare class `Order_Amount` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Order_Amount extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_by_order_amount';
			$this->short_desc = __( 'Shipping Methods by Min/Max Order Amount', 'all-in-one-wc' );
			$this->desc       = __( 'Set minimum and/or maximum order amount for shipping methods to show up (Local pickup available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set minimum and/or maximum order amount for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-min-max-order-amount';
			parent::__construct();

			if ( $this->is_enabled() ) {
				$this->use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_by_order_amount_use_shipping_instance', 'no' ) );
				add_filter( 'woocommerce_package_rates', array( $this, 'available_shipping_methods' ), PHP_INT_MAX, 2 );
			}
		}

		/**
		 * Available shipping methods.
		 *
		 * @param array  $rates Rates.
		 * @param string $package Package.
		 * @return string
		 */
		function available_shipping_methods( $rates, $package ) {
			if ( ! isset( WC()->cart ) || WC()->cart->is_empty() ) {
				return $rates;
			}
			$total_in_cart = WC()->cart->cart_contents_total;
			foreach ( $rates as $rate_key => $rate ) {
				$min = ( $this->use_shipping_instances ?
					get_option( 'aiow_shipping_by_order_amount_min_instance_' . $rate->instance_id, 0 ) : aiow_option( 'aiow_shipping_by_order_amount_min_' . $rate->method_id, 0 ) );
				$max = ( $this->use_shipping_instances ?
					get_option( 'aiow_shipping_by_order_amount_max_instance_' . $rate->instance_id, 0 ) : aiow_option( 'aiow_shipping_by_order_amount_max_' . $rate->method_id, 0 ) );
				if ( 0 != $min && $total_in_cart < $min ) {
					unset( $rates[ $rate_key ] );
				} elseif ( 0 != $max && $total_in_cart > $max ) {
					unset( $rates[ $rate_key ] );
				}
			}
			return $rates;
		}
	}
}
