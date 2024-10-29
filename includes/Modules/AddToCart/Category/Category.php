<?php
/**
 * All In One For WooCommerce Add to Cart per Category
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\AddToCart\Category;

if ( ! class_exists( 'Category' ) ) :

class Category {

	/**
	 * Constructor.
	 */
	function __construct() {
		if ( 'yes' === aiow_option( 'aiow_add_to_cart_per_category_enabled' ) ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_button_text_single' ),  PHP_INT_MAX );
			add_filter( 'woocommerce_product_add_to_cart_text',        array( $this, 'change_add_to_cart_button_text_archive' ), PHP_INT_MAX );
		}
	}

	/**
	 * Change add to cart button text.
	 *
	 * @param string $add_to_cart_text Add to cart.
	 * @return string
	 */
	function change_add_to_cart_button_text_single( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'single' );
	}

	/**
	 * Change add to cart button text.
	 *
	 * @param string $add_to_cart_text Add to cart.
	 * @return string
	 */
	function change_add_to_cart_button_text_archive( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'archive' );
	}

	/**
	 * Change add to cart button text.
	 *
	 * @param string $add_to_cart_text Add to cart button.
	 * @param string $single_or_archive Single OR Archive
	 * @return string
	 */
	function change_add_to_cart_button_text( $add_to_cart_text, $single_or_archive ) {
		$product_categories = get_the_terms( get_the_ID(), 'product_cat' );
		if ( empty( $product_categories ) ) return $add_to_cart_text;
		for ( $i = 1; $i <= apply_filters( 'aiow_option', 1, aiow_option( 'aiow_add_to_cart_per_category_total_groups_number', 1 ) ); $i++ ) {
			if ( 'yes' !== aiow_option( 'aiow_add_to_cart_per_category_enabled_group_' . $i ) ) continue;
			$categories = aiow_option( 'aiow_add_to_cart_per_category_ids_group_' . $i );
			if ( empty(  $categories ) ) continue;
			foreach ( $product_categories as $product_category ) {
				foreach ( $categories as $category ) {
					if ( $product_category->term_id == $category ) {
						return aiow_option( 'aiow_add_to_cart_per_category_text_' . $single_or_archive . '_group_' . $i, $add_to_cart_text );
					}
				}
			}
		}
		return $add_to_cart_text;
	}
}

endif;

