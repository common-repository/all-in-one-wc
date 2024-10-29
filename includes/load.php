<?php
/**
 * All In One For WooCommerce Constants
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! defined( 'AIOW_WC_VERSION' ) ) {
	define( 'AIOW_WC_VERSION', aiow_option( 'woocommerce_version', null ) );
}

if ( ! defined( 'AIOW_IS_WC_VERSION_BELOW_3' ) ) {
	define( 'AIOW_IS_WC_VERSION_BELOW_3', version_compare( AIOW_WC_VERSION, '3.0.0', '<' ) );
}

if ( ! defined( 'AIOW_IS_WC_VERSION_BELOW_3_2_0' ) ) {
	define( 'AIOW_IS_WC_VERSION_BELOW_3_2_0', version_compare( AIOW_WC_VERSION, '3.2.0', '<' ) );
}

if ( ! defined( 'AIOW_IS_WC_VERSION_BELOW_3_3_0' ) ) {
	define( 'AIOW_IS_WC_VERSION_BELOW_3_3_0', version_compare( AIOW_WC_VERSION, '3.3.0', '<' ) );
}

if ( ! defined( 'AIOW_IS_WC_VERSION_BELOW_3_4_0' ) ) {
	define( 'AIOW_IS_WC_VERSION_BELOW_3_4_0', version_compare( AIOW_WC_VERSION, '3.4.0', '<' ) );
}

if ( ! defined( 'AIOW_PRODUCT_GET_PRICE_FILTER' ) ) {
	define( 'AIOW_PRODUCT_GET_PRICE_FILTER', ( AIOW_IS_WC_VERSION_BELOW_3 ? 'woocommerce_get_price' : 'woocommerce_product_get_price' ) );
}

if ( ! defined( 'AIOW_PRODUCT_GET_SALE_PRICE_FILTER' ) ) {
	define( 'AIOW_PRODUCT_GET_SALE_PRICE_FILTER', ( AIOW_IS_WC_VERSION_BELOW_3 ? 'woocommerce_get_sale_price' : 'woocommerce_product_get_sale_price' ) );
}

if ( ! defined( 'AIOW_PRODUCT_GET_REGULAR_PRICE_FILTER' ) ) {
	define( 'AIOW_PRODUCT_GET_REGULAR_PRICE_FILTER', ( AIOW_IS_WC_VERSION_BELOW_3 ? 'woocommerce_get_regular_price' : 'woocommerce_product_get_regular_price' ) );
}

if ( ! defined( 'AIOW_SESSION_TYPE' ) ) {
	define( 'AIOW_SESSION_TYPE', ( 'yes' === aiow_option( 'aiow_general_enabled', 'no' ) ? aiow_option( 'aiow_general_advanced_session_type', 'standard' ) : 'standard' ) );
}

if ( ! defined( 'AIOW_VERSION_OPTION' ) ) {
	define( 'AIOW_VERSION_OPTION', ( 'all-in-one-wc.php' === basename( AIOW_PLUGIN_FILE ) ? 'aiow_for_woocommerce_version' : 'aiow_plus_for_woocommerce_version' ) );
}

// Core Module for WooCommerce.
$this->modules = aiow_module_lists();
// Shortcode.
$this->shortcodes = aiow_register_shortcodes();
// Load module with status.
$this->aiow_load_modules();
// Load admin module.
$admin = new AIOW\Admin\Admin();
