<?php
/**
 * Shipping Module - Shipping Icons.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Icons' ) ) {

	/**
	 * Declare class `Icons` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Icons extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_icons';
			$this->short_desc = __( 'Shipping Icons', 'all-in-one-wc' );
			$this->desc       = __( 'Add icons to shipping methods on frontend. Icon Visibility (Plus)', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Add icons to shipping methods on frontend.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-icons';
			parent::__construct();
			if ( $this->is_enabled() ) {
				add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shipping_icon' ), PHP_INT_MAX, 2 );
			}
		}

		/**
		 * Add shipping icon.
		 *
		 * @param string $label Label.
		 * @param object $method Object.
		 * @return string
		 */
		function shipping_icon( $label, $method ) {
			$shipping_icons_visibility = apply_filters( 'aiow_option', 'both', aiow_option( 'aiow_shipping_icons_visibility', 'both' ) );
			if ( 'checkout_only' === $shipping_icons_visibility && is_cart() ) {
				return $label;
			}
			if ( 'cart_only' === $shipping_icons_visibility && is_checkout() ) {
				return $label;
			}
			$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_icons_use_shipping_instance', 'no' ) );
			$option_id              = 'aiow_shipping_icon_' . ( $use_shipping_instances ? 'instance_' . $method->instance_id : $method->method_id );
			if ( '' != ( $icon_url = aiow_option( $option_id, '' ) ) ) {
				$style_html = ( '' != ( $style = aiow_option( 'aiow_shipping_icons_style', 'display:inline;' ) ) ) ?  'style="' . $style . '" ' : '';
				$img = '<img ' . $style_html . 'class="aiow_shipping_icon" id="' . $option_id . '" src="' . $icon_url . '">';
				$label = ( 'before' === aiow_option( 'aiow_shipping_icons_position', 'before' ) ) ? $img . ' ' . $label : $label . ' ' . $img;
			}
			return $label;
		}
	}
}
