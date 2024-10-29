<?php
/**
 * All In One For WooCommerce - Functions - Admin
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_get_plus_message' ) ) {
	/**
	 * Get pro plugin message.
	 *
	 * @param string $value Value.
	 * @param string $message_type Message type.
	 * @param array  $args Data.
	 * @return string
	 */
	function aiow_get_plus_message( $value, $message_type, $args = array() ) {
		return $value;
	}
}

if ( ! function_exists( 'aiow_message_replaced_values' ) ) {
	/**
	 * Message replace values.
	 *
	 * @param array $values Values.
	 * @return string
	 */
	function aiow_message_replaced_values( $values ) {
		$message_template = ( 1 == count( $values ) ? __( 'Replaced value: %s', 'all-in-one-wc' ) : __( 'Replaced values: %s', 'all-in-one-wc' ) );
		return sprintf( $message_template, '<code>' . implode( '</code>, <code>', $values ) . '</code>' );
	}
}


if ( ! function_exists( 'aiow_get_ajax_settings' ) ) {
	/**
	 * Get ajax settings
	 *
	 * @param array  $values Values.
	 * @param bool   $allow_multiple_values Allow multiple.
	 * @param string $search_type Possible values 'woocommerce_json_search_products', 'woocommerce_json_search_products_and_variations' , 'woocommerce_json_search_categories', 'woocommerce_json_search_customers'.
	 *
	 * @return array
	 */
	function aiow_get_ajax_settings( $values, $allow_multiple_values = false, $search_type = 'woocommerce_json_search_products' ) {
		$options_raw = aiow_option( $values['id'], isset( $values['default'] ) ? $values['default'] : '' );
		$options_raw = empty( $options_raw ) ? array() : $options_raw;
		$options     = array();
		$class       = '';
		if ( $search_type == 'woocommerce_json_search_products' || $search_type == 'woocommerce_json_search_products_and_variations' ) {
			$class = 'wc-product-search';
			if( $options_raw ) {
				foreach ( $options_raw as $product_id ) {
					$product = wc_get_product( $product_id );
					if ( is_a( $product, 'WC_Product' ) ) {
						$options[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
					}
				}
			}
		} elseif ( $search_type == 'woocommerce_json_search_categories' ) {
			$class = 'wc-category-search';
			foreach ( $options_raw as $term_id ) {
				$term                = get_term_by( 'slug', $term_id, 'product_cat' );
				$options[ $term_id ] = wp_kses_post( $term->name );
			}
		} elseif ( $search_type == 'woocommerce_json_search_customers' ) {
			$class = 'wc-customer-search';
			foreach ( $options_raw as $term_id ) {
				$user                = get_user_by( 'id', $term_id );
				$options[ $term_id ] = wp_kses_post( $user->display_name );
			}
		}
		$placeholder = isset( $values['placeholder'] ) ? isset( $values['placeholder'] ) : __( "Search&hellip;", 'all-in-one-wc' );
		return array_merge( $values, array(
			'custom_attributes'            => array(
				'data-action'      => $search_type,
				'data-allow_clear' => "true",
				'aria-hidden'      => "true",
				'data-sortable'    => "true",
				'data-placeholder' => $placeholder
			),
			'type'                         => $allow_multiple_values ? 'multiselect' : 'select',
			'options'                      => $options,
			'class'                        => $class,
			'ignore_enhanced_select_class' => true,
		) );
	}
}
