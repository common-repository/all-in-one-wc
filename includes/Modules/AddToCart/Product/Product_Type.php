<?php
/**
 * All In One For WooCommerce - Add to Cart Button Labels - Per Product Type
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\AddToCart\Product;

if ( ! class_exists( 'Product_Type' ) ) :

class Product_Type {

	/**
	 * Constructor.
	 */
	function __construct() {
		if ( 'yes' === aiow_option( 'aiow_add_to_cart_text_enabled', 'no' ) ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_add_to_cart_button_text' ), 100 );
			add_filter( 'woocommerce_product_add_to_cart_text',        array( $this, 'custom_add_to_cart_button_text' ), 100 );
		}
	}

	/**
	 * Custom add to cart button.
	 *
	 * @param string $add_to_cart_text Add to cart text.
	 * @return string
	 */
	function custom_add_to_cart_button_text( $add_to_cart_text ) {

		global $woocommerce, $product;
		$product = is_string( $product ) ? wc_get_product( get_the_ID() ) : $product;

		if ( ! $product || is_string( $product ) ) {
			return $add_to_cart_text;
		}

		$product_type = ( AIOW_IS_WC_VERSION_BELOW_3 ? $product->product_type : $product->get_type() );
		if ( ! in_array( $product_type, array( 'external', 'grouped', 'simple', 'variable' ) ) ) {
			$product_type = 'other';
		}

		$single_or_archive = ( 'woocommerce_product_single_add_to_cart_text' == current_filter() ? 'single' : 'archives' );

		// Already in cart
		if ( '' != ( $text_already_in_cart = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_in_cart_' . $product_type, '' ) ) && isset( $woocommerce->cart ) ) {
			foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if( get_the_ID() == aiow_get_product_id_or_variation_parent_id( $_product ) ) {
					return do_shortcode( $text_already_in_cart );
				}
			}
		}

		// Not in stock
		if ( '' != ( $text_on_not_in_stock = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_not_in_stock_' . $product_type, '' ) ) && ! $product->is_in_stock() ) {
			return do_shortcode( $text_on_not_in_stock );
		}

		// On sale
		if ( '' != ( $text_on_sale = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_sale_' . $product_type, '' ) ) && $product->is_on_sale() ) {
			return do_shortcode( $text_on_sale );
		}

		// Empty price
		if ( '' != ( $text_on_no_price = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_no_price_' . $product_type, '' ) ) && '' === $product->get_price() ) {
			return do_shortcode( $text_on_no_price );
		}

		// Free (i.e. zero price)
		if ( '' != ( $text_on_zero_price = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_zero_price_' . $product_type, '' ) ) && 0 == $product->get_price() ) {
			return do_shortcode( $text_on_zero_price );
		}

		// General
		if ( '' != ( $text_general = aiow_option( 'aiow_add_to_cart_text_on_' . $single_or_archive . '_' . $product_type, '' ) ) ) {
			return do_shortcode( $text_general );
		}

		// Default
		return $add_to_cart_text;
	}
}

endif;
