<?php
/**
 * Register Cart Shortcode
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Shortcodes;

if ( ! class_exists( 'Cart' ) ) {

	/**
	 * Declare class Cart`` extends to `Shortcodes`
	 */
	class Cart extends Shortcodes {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->the_shortcodes = array(
				'aiow_cart_discount_tax',
				'aiow_cart_discount_total',
				'aiow_cart_items_total_quantity',
				'aiow_cart_items_total_weight',
				'aiow_cart_fee_tax',
				'aiow_cart_fee_total',
				'aiow_cart_function',
				'aiow_cart_shipping_total',
				'aiow_cart_shipping_tax',
				'aiow_cart_subtotal',
				'aiow_cart_subtotal_tax',
				'aiow_cart_tax',
				'aiow_cart_total',
				'aiow_cart_total_ex_tax',
			);

			$this->the_atts = array(
				'multiply_by' => 1,
			);

			parent::__construct();

		}

		/**
		 * Get cart function.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_function( $atts ) {
			if ( isset( $atts['function_name'] ) && '' != $atts['function_name'] && ( $_cart = WC()->cart ) ) {
				return $_cart->{$atts['function_name']}();
			}
			return '';
		}

		/**
		 * Cart tex fee.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_fee_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_fee_tax() );
			}
			return '';
		}

		/**
		 * Get cart fee total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_fee_total( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_fee_total() );
			}
			return '';
		}

		/**
		 * Get cart discount tax.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_discount_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_discount_tax() );
			}
			return '';
		}

		/**
		 * Get cart discount total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_discount_total( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_discount_total() );
			}
			return '';
		}

		/**
		 * Get cart total quantity.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_items_total_quantity( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_cart_contents_count();
			}
			return '';
		}

		/**
		 * Get cart total weight.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_items_total_weight( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_cart_contents_weight();
			}
			return '';
		}

		/**
		 * Get cart total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_total( $atts ) {
			if ( $_cart = WC()->cart ) {
				if ( 1 != $atts['multiply_by'] ) {
					// `get_cart_contents_total()` - Gets cart total. This is the total of items in the cart, but after discounts. Subtotal is before discounts.
					$cart_total = wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total();
					return wc_price( $atts['multiply_by'] * $cart_total );
				} else {
					return $_cart->get_cart_total();
				}
			}
			return '';
		}

		/**
		 * Get cart sub total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_subtotal( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_cart_subtotal();
			}
			return '';
		}

		/**
		 * Get cart sub total tax.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_subtotal_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_subtotal_tax() );
			}
			return '';
		}

		/**
		 * Get cart tax.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_cart_tax();
			}
			return '';
		}

		/**
		 * Get cart total extra tax.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_total_ex_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_total_ex_tax();
			}
			return '';
		}

		/**
		 * Get cart shipping total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_shipping_total( $atts ) {
			if ( $_cart = WC()->cart ) {
				return $_cart->get_cart_shipping_total();
			}
			return '';
		}

		/**
		 * Get cart shipping tax.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_cart_shipping_tax( $atts ) {
			if ( $_cart = WC()->cart ) {
				return wc_price( $_cart->get_shipping_tax() );
			}
			return '';
		}
	}
}
