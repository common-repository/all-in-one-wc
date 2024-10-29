<?php
/**
 * Shipping Module - Shipping Time.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Time' ) ) {

	/**
	 * Declare class `Time` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Time extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_time';
			$this->short_desc = __( 'Shipping Time', 'all-in-one-wc' );
			$this->extra_desc = sprintf( __( 'After you set estimated shipping time here, you can display it on frontend with %s shortcodes.', 'all-in-one-wc' ),
			'<code>[aiow_shipping_time_table]</code>, <code>[aiow_product_shipping_time_table]</code>' );
			$this->desc       = __( 'Add delivery time estimation to shipping methods.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-time';
			parent::__construct();
		}
	}
}
