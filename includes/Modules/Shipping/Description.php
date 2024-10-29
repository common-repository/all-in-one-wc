<?php
/**
 * Shipping Module - Shipping Descriptions.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Description' ) ) {

	/**
	 * Declare class `Description` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Description extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_description';
			$this->short_desc = __( 'Shipping Descriptions', 'all-in-one-wc' );
			$this->desc       = __( 'Add descriptions to shipping methods on frontend. Description visibility (Plus). Description position (Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Add descriptions to shipping methods on frontend.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-descriptions';
			parent::__construct();
			if ( $this->is_enabled() ) {
				$this->shipping_descriptions_visibility = apply_filters( 'aiow_option', 'both', aiow_option( 'aiow_shipping_descriptions_visibility', 'both' ) );
				$this->shipping_descriptions_position   = apply_filters( 'aiow_option', 'after', aiow_option( 'aiow_shipping_descriptions_position', 'after' ) );
				add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shipping_description' ), PHP_INT_MAX, 2 );
			}
		}

		/**
		 * Add shipping description.
		 *
		 * @param string $label Label.
		 * @param object $method Method object.
		 * @return string
		 */
		function shipping_description( $label, $method ) {
			if ( 'checkout_only' === $this->shipping_descriptions_visibility && is_cart() ) {
				return $label;
			}
			if ( 'cart_only' === $this->shipping_descriptions_visibility && is_checkout() ) {
				return $label;
			}
			$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_descriptions_use_shipping_instance', 'no' ) );
			$option_id              = 'aiow_shipping_description_' . ( $use_shipping_instances ? 'instance_' . $method->instance_id : $method->method_id );
			if ( '' != ( $desc = aiow_option( $option_id, '' ) ) ) {
				switch ( $this->shipping_descriptions_position ) {
					case 'before':
						return $desc . $label;
					case 'instead':
						return $desc;
					default:
						return $label . $desc;
				}
			} else {
				return $label;
			}
		}
	}
}
