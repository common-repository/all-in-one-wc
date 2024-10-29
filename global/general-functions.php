<?php
/**
 * All In One For WooCommerce - Functions - General
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_is_frontend' ) ) {
	/**
	 * Check is frontend OR not.
	 */
	function aiow_is_frontend() {
		if ( ! is_admin() ) {
			return true;
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return ( ! isset( $_REQUEST['action'] ) || ! is_string( $_REQUEST['action'] ) || ! in_array( $_REQUEST['action'], array(
				'woocommerce_load_variations',
			) ) );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'aiow_is_module_deprecated' ) ) {
	/**
	 * Check is deprecated function.
	 *
	 * @param string $module_id Module ID.
	 * @param bool   $by_module_option Module option.
	 * @param bool   $check_for_disabled Check for disabled.
	 * @return bool
	 */
	function aiow_is_module_deprecated( $module_id, $by_module_option = false, $check_for_disabled = false ) {
		if ( $check_for_disabled ) {
			$module_option = ( $by_module_option ? $module_id : 'aiow_' . $module_id . '_enabled' );
			if ( 'yes' === aiow_option( $module_option, 'no' ) ) {
				return false;
			}
		}
		if ( $by_module_option ) {
			$module_id = str_replace( array( 'aiow_', '_enabled' ), '', $module_id );
		}
		$deprecated_and_replacement_modules = array(
			'product_info' => array(
				'cat'    => 'products',
				'module' => 'product_custom_info',
				'title'  => __( 'Product Info', 'all-in-one-wc' ),
			),
		);
		if ( ! array_key_exists( $module_id, $deprecated_and_replacement_modules ) ) {
			return false;
		} else {
			return ( isset( $deprecated_and_replacement_modules[ $module_id ] ) ? $deprecated_and_replacement_modules[ $module_id ] : array() );
		}
	}
}

if ( ! function_exists( 'aiow_handle_deprecated_options' ) ) {
	/**
	 * Handle deprecated options.
	 */
	function aiow_handle_deprecated_options() {
		foreach ( AIOW()->modules as $module ) {
			$module->handle_deprecated_options();
		}
	}
}

if ( ! function_exists( 'aiow_get_aiow_uploads_dir' ) ) {
	/**
	 * Plugin upload dir.
	 *
	 * @param string $subdir Sub Dir.
	 * @param bool   $do_mkdir Create new dir.
	 * @return string
	 */
	function aiow_get_aiow_uploads_dir( $subdir = '', $do_mkdir = true ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$upload_dir = $upload_dir . '/woocommerce_uploads';
		if ( $do_mkdir && ! file_exists( $upload_dir ) ) {
			mkdir( $upload_dir, 0755, true );
		}
		$upload_dir = $upload_dir . '/aiow_uploads';
		if ( $do_mkdir && ! file_exists( $upload_dir ) ) {
			mkdir( $upload_dir, 0755, true );
		}
		if ( '' != $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir;
			if ( $do_mkdir && ! file_exists( $upload_dir ) ) {
				mkdir( $upload_dir, 0755, true );
			}
		}
		return $upload_dir;
	}
}

if ( ! function_exists( 'aiow_hex2rgb' ) ) {
	/**
	 * HEX TO RGB.
	 *
	 * @param string $hex Hex code.
	 * @return string
	 */
	function aiow_hex2rgb( $hex ) {
		return sscanf( $hex, '#%2x%2x%2x' );
	}
}

if ( ! function_exists( 'aiow_get_cart_filters' ) ) {
	/**
	 * Get cart filter.
	 */
	function aiow_get_cart_filters() {
		return array(
			'woocommerce_before_cart'                    => __( 'Before cart', 'all-in-one-wc' ),
			'woocommerce_before_cart_table'              => __( 'Before cart table', 'all-in-one-wc' ),
			'woocommerce_before_cart_contents'           => __( 'Before cart contents', 'all-in-one-wc' ),
			'woocommerce_cart_contents'                  => __( 'Cart contents', 'all-in-one-wc' ),
			'woocommerce_cart_coupon'                    => __( 'Cart coupon', 'all-in-one-wc' ),
			'woocommerce_cart_actions'                   => __( 'Cart actions', 'all-in-one-wc' ),
			'woocommerce_after_cart_contents'            => __( 'After cart contents', 'all-in-one-wc' ),
			'woocommerce_after_cart_table'               => __( 'After cart table', 'all-in-one-wc' ),
			'woocommerce_cart_collaterals'               => __( 'Cart collaterals', 'all-in-one-wc' ),
			'woocommerce_after_cart'                     => __( 'After cart', 'all-in-one-wc' ),

			'woocommerce_before_cart_totals'             => __( 'Before cart totals', 'all-in-one-wc' ),
			'woocommerce_cart_totals_before_shipping'    => __( 'Cart totals: Before shipping', 'all-in-one-wc' ),
			'woocommerce_cart_totals_after_shipping'     => __( 'Cart totals: After shipping', 'all-in-one-wc' ),
			'woocommerce_cart_totals_before_order_total' => __( 'Cart totals: Before order total', 'all-in-one-wc' ),
			'woocommerce_cart_totals_after_order_total'  => __( 'Cart totals: After order total', 'all-in-one-wc' ),
			'woocommerce_proceed_to_checkout'            => __( 'Proceed to checkout', 'all-in-one-wc' ),
			'woocommerce_after_cart_totals'              => __( 'After cart totals', 'all-in-one-wc' ),

			'woocommerce_before_shipping_calculator'     => __( 'Before shipping calculator', 'all-in-one-wc' ),
			'woocommerce_after_shipping_calculator'      => __( 'After shipping calculator', 'all-in-one-wc' ),

			'woocommerce_cart_is_empty'                  => __( 'If cart is empty', 'all-in-one-wc' ),
		);
	}
}

