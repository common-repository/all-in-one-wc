<?php
/**
 * All In One For WooCommerce - Functions - Products
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_get_product_id_or_variation_parent_id' ) ) {
	/**
	 * Get variation ID OR Product ID.
	 *
	 * @param object $_product Product object.
	 * @return int
	 */
	function aiow_get_product_id_or_variation_parent_id( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		if ( AIOW_IS_WC_VERSION_BELOW_3 ) {
			return $_product->id;
		} else {
			return ( $_product->is_type( 'variation' ) ) ? $_product->get_parent_id() : $_product->get_id();
		}
	}
}


if ( ! function_exists( 'aiow_get_terms' ) ) {
	/**
	 * Get terms.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	function aiow_get_terms( $args ) {
		if ( ! is_array( $args ) ) {
			$_taxonomy = $args;
			$args = array(
				'taxonomy'   => $_taxonomy,
				'orderby'    => 'name',
				'hide_empty' => false,
			);
		}
		global $wp_version;
		if ( version_compare( $wp_version, '4.5.0', '>=' ) ) {
			$_terms = get_terms( $args );
		} else {
			$_taxonomy = $args['taxonomy'];
			unset( $args['taxonomy'] );
			$_terms = get_terms( $_taxonomy, $args );
		}
		$_terms_options = array();
		if ( ! empty( $_terms ) && ! is_wp_error( $_terms ) ) {
			foreach ( $_terms as $_term ) {
				$_terms_options[ $_term->term_id ] = $_term->name;
			}
		}
		return $_terms_options;
	}
}


if ( ! function_exists( 'aiow_get_products' ) ) {
	/**
	 * Get products.
	 *
	 * @param array   $products Products.
	 * @param string  $post_status Post status.
	 * @param int     $block_size Block size.
	 * @param bool    $add_variations Product variations.
	 * @param bool    $variations_only variations only.
	 * @return array
	 */
	function aiow_get_products( $products = array(), $post_status = 'any', $block_size = 256, $add_variations = false, $variations_only = false ) {
		$offset = 0;
		while( true ) {
			$args = array(
				'post_type'      => ( $add_variations ? array( 'product', 'product_variation' ) : 'product' ),
				'post_status'    => $post_status,
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $post_id ) {
				if ( $variations_only ) {
					$_product = wc_get_product( $post_id );
					if ( $_product->is_type( 'variable' ) ) {
						continue;
					}
				}
				$products[ $post_id ] = get_the_title( $post_id ) . ' (ID:' . $post_id . ')';
			}
			$offset += $block_size;
		}
		return $products;
	}
}
