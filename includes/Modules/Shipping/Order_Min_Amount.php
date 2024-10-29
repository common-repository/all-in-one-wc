<?php
/**
 * Shipping Module - Order Minimum Amount.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Order_Min_Amount' ) ) {

	/**
	 * Declare class `Order_Min_Amount` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Order_Min_Amount extends \AIOW\Modules\Register_Modules {

		/**
		 * Gift card discount.
		 *
		 * @var int $yith_gift_card_discount
		 */
		private $yith_gift_card_discount = 0;

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'order_min_amount';
			$this->short_desc = __( 'Order Minimum Amount', 'all-in-one-wc' );
			$this->desc       = __( 'Minimum order amount. Order Minimum Amount by User Role (Administrator, Guest and Customer available in free version).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Minimum order amount (optionally by user role) .', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-order-minimum-amount';
			parent::__construct();
			if ( $this->is_enabled() ) {
				add_action( 'init', array( $this, 'add_order_minimum_amount_hooks' ) );
			}
		}

		/**
		 * Add order minimum amount hooks.
		 */
		function add_order_minimum_amount_hooks() {
			$is_order_minimum_amount_enabled = false;
			if ( aiow_option( 'aiow_order_minimum_amount', 0 ) > 0 ) {
				$is_order_minimum_amount_enabled = true;
			} else {
				foreach ( aiow_get_user_roles() as $role_key => $role_data ) {
					if ( aiow_option( 'aiow_order_minimum_amount_by_user_role_' . $role_key, 0 ) > 0 ) {
						$is_order_minimum_amount_enabled = true;
						break;
					}
				}
			}
			if ( $is_order_minimum_amount_enabled ) {
				add_action( 'woocommerce_checkout_process', array( $this, 'order_minimum_amount' ) );
				add_action( 'woocommerce_before_cart',      array( $this, 'order_minimum_amount' ) );
				if ( 'yes' === aiow_option( 'aiow_order_minimum_amount_stop_from_seeing_checkout', 'no' ) ) {
					add_action( 'wp', array( $this, 'stop_from_seeing_checkout' ), 100 );
				}
			}
			add_action( 'yith_ywgc_apply_gift_card_discount_after_cart_total', array( $this, 'get_yith_gift_cards_discount' ), 10, 2 );
		}

		/**
		 * Get yith gift cards discount.
		 *
		 * @param object $cart Cart data.
		 * @param string $discount Discount.
		 */
		function get_yith_gift_cards_discount( $cart, $discount ) {
			$this->yith_gift_card_discount = $discount;
		}

		/**
		 * Get order minimum amount with user roles.
		 */
		function get_order_minimum_amount_with_user_roles() {
			$minimum = aiow_option( 'aiow_order_minimum_amount', 0 );
			$current_user_role = aiow_get_current_user_first_role();
			foreach ( aiow_get_user_roles() as $role_key => $role_data ) {
				if ( $role_key === $current_user_role ) {
					$order_minimum_amount_by_user_role = aiow_option( 'aiow_order_minimum_amount_by_user_role_' . $role_key, 0 );
					if ( $order_minimum_amount_by_user_role > 0 ) {
						$minimum = $order_minimum_amount_by_user_role;
					}
					break;
				}
			}
			// Multicurrency.
			/*if ( AIOW()->modules['multicurrency']->is_enabled() ) {
				$minimum = AIOW()->modules['multicurrency']->change_price( $minimum, null );
			}*/
			// Price by country module.
			/*if ( AIOW()->modules['price_by_country']->is_enabled() ) {
				$minimum = AIOW()->modules['price_by_country']->core->change_price( $minimum, null );
			}*/
			// WooCommerce Multilingual.
			if ( 'yes' === aiow_option( 'aiow_order_minimum_compatibility_wpml_multilingual', 'no' ) ) {
				global $woocommerce_wpml;
				$minimum = ! empty( $woocommerce_wpml ) ? $woocommerce_wpml->multi_currency->prices->convert_price_amount( $minimum ) : $minimum;
			}

			return $minimum;
		}

		/**
		 * Get cart total for minimal order amount.
		 *
		 * @return mixed
		 */
		function get_cart_total_for_minimal_order_amount() {
			if ( ! isset( WC()->cart ) ) {
				return 0;
			}
			WC()->cart->calculate_totals();
			$cart_total = WC()->cart->total;
			if ( 'yes' === aiow_option( 'aiow_order_minimum_amount_exclude_shipping', 'no' ) ) {
				$shipping_total     = isset( WC()->cart->shipping_total )     ? WC()->cart->shipping_total     : 0;
				$shipping_tax_total = isset( WC()->cart->shipping_tax_total ) ? WC()->cart->shipping_tax_total : 0;
				$cart_total -= ( $shipping_total + $shipping_tax_total );
			}
			if ( 'yes' === aiow_option( 'aiow_order_minimum_amount_exclude_discounts', 'no' ) ) {
				$cart_total += ( WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total() );
			}
			if ('yes' === aiow_option( 'aiow_order_minimum_amount_exclude_yith_gift_card_discount', 'no' ) ) {
				$cart_total += $this->yith_gift_card_discount;
			}
			return $cart_total;
		}

		/**
		 * Order minimum amount.
		 *
		 * @return mixed
		 */
		function order_minimum_amount() {
			$minimum = $this->get_order_minimum_amount_with_user_roles();
			if ( 0 == $minimum ) {
				return;
			}
			$cart_total = $this->get_cart_total_for_minimal_order_amount();
			if ( $cart_total < $minimum ) {
				if ( is_cart() ) {
					if ( 'yes' === aiow_option( 'aiow_order_minimum_amount_cart_notice_enabled', 'no' ) ) {
						$notice_function = aiow_option( 'aiow_order_minimum_amount_cart_notice_function', 'wc_print_notice' );
						$notice_function(
							sprintf( apply_filters( 'aiow_option', 'You must have an order with a minimum of %s to place your order, your current order total is %s.', aiow_option( 'aiow_order_minimum_amount_cart_notice_message' ) ),
								wc_price( $minimum ),
								wc_price( $cart_total )
							),
							get_option( 'aiow_order_minimum_amount_cart_notice_type', 'notice' )
						);
					}
				} else {
					wc_add_notice(
						sprintf( apply_filters( 'aiow_option', 'You must have an order with a minimum of %s to place your order, your current order total is %s.', aiow_option( 'aiow_order_minimum_amount_error_message' ) ),
							wc_price( $minimum ),
							wc_price( $cart_total )
						),
						'error'
					);
				}
			}
		}

		/**
		 * Stop from seeing checkout.
		 *
		 * @param object $wp WP Data.
		 */
		function stop_from_seeing_checkout( $wp ) {
			global $woocommerce;
			if ( ! isset( $woocommerce ) || ! is_object( $woocommerce ) ) {
				return;
			}
			if ( ! isset( $woocommerce->cart ) || ! is_object( $woocommerce->cart ) ) {
				return;
			}
			if ( ! is_checkout() ) {
				return;
			}
			$minimum = $this->get_order_minimum_amount_with_user_roles();
			if ( 0 == $minimum ) {
				return;
			}
			$the_cart_total = $this->get_cart_total_for_minimal_order_amount();
			if ( 0 == $the_cart_total ) {
				return;
			}
			if ( $the_cart_total < $minimum ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}

	}
}
