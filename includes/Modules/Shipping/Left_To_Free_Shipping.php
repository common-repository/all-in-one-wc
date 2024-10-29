<?php
/**
 * Shipping Module - Left to Free Shipping.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Left_To_Free_Shipping' ) ) {

	/**
	 * Declare class `Left_To_Free_Shipping` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Left_To_Free_Shipping extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'left_to_free_shipping';
			$this->short_desc = __( 'Left to Free Shipping', 'all-in-one-wc' );
			$this->desc       = __( 'Display "left to free shipping" info.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-left-to-free-shipping';
			parent::__construct();

			if ( $this->is_enabled() ) {
				if ( 'yes' === aiow_option( 'aiow_shipping_left_to_free_info_enabled_cart', 'no' ) ) {
					add_action(
						get_option( 'aiow_shipping_left_to_free_info_position_cart', 'woocommerce_after_cart_totals' ),
						array( $this, 'show_left_to_free_shipping_info_cart' ),
						get_option( 'aiow_shipping_left_to_free_info_priority_cart', 10 )
					);
				}
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_shipping_left_to_free_info_enabled_mini_cart', 'no' ) ) ) {
					add_action(
						get_option( 'aiow_shipping_left_to_free_info_position_mini_cart', 'woocommerce_after_mini_cart' ),
						array( $this, 'show_left_to_free_shipping_info_mini_cart' ),
						get_option( 'aiow_shipping_left_to_free_info_priority_mini_cart', 10 )
					);
				}
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_shipping_left_to_free_info_enabled_checkout', 'no' ) ) ) {
					add_action(
						get_option( 'aiow_shipping_left_to_free_info_position_checkout', 'woocommerce_checkout_after_order_review' ),
						array( $this, 'show_left_to_free_shipping_info_checkout' ),
						get_option( 'aiow_shipping_left_to_free_info_priority_checkout', 10 )
					);
				}
			}
		}

		/**
		 * Show left to free shipping info checkout.
		 */
		function show_left_to_free_shipping_info_checkout() {
			$this->show_left_to_free_shipping_info( do_shortcode( aiow_option( 'aiow_shipping_left_to_free_info_content_checkout', __( '%left_to_free% left to free shipping', 'all-in-one-wc' ) ) ) );
		}

		/**
		 * Show left to free shipping info mini cart.
		 */
		function show_left_to_free_shipping_info_mini_cart() {
			$this->show_left_to_free_shipping_info( do_shortcode( aiow_option( 'aiow_shipping_left_to_free_info_content_mini_cart', __( '%left_to_free% left to free shipping', 'all-in-one-wc' ) ) ) );
		}

		/**
		 * Show left to free shipping info cart.
		 */
		function show_left_to_free_shipping_info_cart() {
			$this->show_left_to_free_shipping_info( do_shortcode( aiow_option( 'aiow_shipping_left_to_free_info_content_cart', __( '%left_to_free% left to free shipping', 'all-in-one-wc' ) ) ) );
		}

		/**
		 * Show left to free shipping info.
		 *
		 * @param string $content Content.
		 */
		function show_left_to_free_shipping_info( $content ) {
			echo aiow_get_left_to_free_shipping( $content );
		}
	}
}
