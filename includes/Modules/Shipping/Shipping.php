<?php
/**
 * Shipping Module - Custom Shipping
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Shipping' ) ) {

	/**
	 * Declare class `Shipping` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Shipping extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping';
			$this->short_desc = __( 'Custom Shipping', 'all-in-one-wc' );
			$this->desc       = __( 'Add multiple custom shipping methods to WooCommerce.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-custom-shipping';
			parent::__construct();

			if ( $this->is_enabled() ) {
				add_filter( 'woocommerce_shipping_methods', array( $this, 'add_wc_shipping_aiow_custom_class' ) );
				// Custom Shipping.
				if ( 'yes' === aiow_option( 'aiow_shipping_custom_shipping_w_zones_enabled', 'no' ) ) {
					add_filter( 'woocommerce_shipping_methods', array( $this, 'add_wc_shipping_aiow_custom_w_zones_class' ) );
				}
			}
		}

		/**
		 * Add shipping class.
		 *
		 * @param array $methods Methods.
		 * @return array
		 */
		function add_wc_shipping_aiow_custom_class( $methods ) {
			$total_number = aiow_option( 'aiow_shipping_custom_shipping_total_number', 1 );
			for ( $i = 1; $i <= $total_number; $i ++ ) {
				$the_method = new \AIOW\Modules\Shipping\Custom_Template();
				$the_method->init( $i );
				$methods[ $the_method->id ] = $the_method;
			}
			return $methods;
		}

		/**
		 * Add WooCommerce shipping zonesclass.
		 *
		 * @param array $methods Methods.
		 * @return array
		 */
		function add_wc_shipping_aiow_custom_w_zones_class( $methods ) {
			$methods['aiow_custom_shipping_w_zones'] = '\AIOW\Modules\Shipping\Shipping_Zones';
			return $methods;
		}
	}
}
