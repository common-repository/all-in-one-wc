<?php
/**
 * All In One For WooCommerce - Module - More Button Labels
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Button;

if ( ! class_exists( 'More_Button_Labels' ) ) :

class More_Button_Labels extends \AIOW\Modules\Register_Modules {

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->id         = 'more_button_labels';
		$this->short_desc = __( 'More Button Labels', 'all-in-one-wc' );
		$this->desc       = __( 'Set "Place order" button label.', 'all-in-one-wc' );
		$this->link_slug  = 'woocommerce-more-button-labels';
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_filter( 'woocommerce_order_button_text', array( $this, 'set_order_button_text' ), PHP_INT_MAX );
			if ( 'yes' === aiow_option( 'aiow_checkout_place_order_button_override', 'no' ) ) {
				add_action( 'init', array( $this, 'override_order_button_text' ), PHP_INT_MAX );
			}
		}
	}

	/**
	 * Overwrite order button text.
	 */
	function override_order_button_text() {
		if ( function_exists( 'WC' ) && method_exists( WC(), 'payment_gateways' ) && isset( WC()->payment_gateways()->payment_gateways ) ) {
			foreach ( WC()->payment_gateways()->payment_gateways as &$payment_gateway ) {
				$payment_gateway->order_button_text = '';
			}
		}
	}

	/**
	 * Set order button text.
	 *
	 * @param string $current_text Current text.
	 * @return string
	 */
	function set_order_button_text( $current_text ) {
		return ( '' != ( $new_text = aiow_option( 'aiow_checkout_place_order_button_text', '' ) ) ) ? $new_text : $current_text;
	}

}

endif;
