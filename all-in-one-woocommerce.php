<?php
/**
 * Plugin Name: All In One For Woocommerce
 * Contributors: dipikaparmar2018
 * Plugin URI: https://wordpress.org/plugins/all-in-one-wc/
 * Description: All In One For WooCommerce plugins many more functionality in one plugins.
 * Author: Dipika Parmar
 * Author URI: https://profiles.wordpress.org/dipikaparmar2018/
 * Text Domain: all-in-one-wc
 * Domain Path: /languages
 * Version: 1.2
 * WC requires at least: 4 or higher
 * WC tested up to: 7.3.0
 *
 * @package         AIOW\Main
 * @subpackage WooCommerce
 */

// If check WordPress install or not.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require autoloaded file.
if ( file_exists( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
}

use AIOW\Main;

if ( ! defined( 'AIOW_PLUGIN_FILE' ) ) {
	/**
	 * AIOW_PLUGIN_FILE.
	 *
	 * @since 1.0
	 */
	define( 'AIOW_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'AIOW_URL' ) ) {
	/**
	 * AIOW_URL.
	 *
	 * @since 1.0
	 */
	define( 'AIOW_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AIOW_BASENAME' ) ) {
	/**
	 * AIOW_BASENAME.
	 *
	 * @since 1.0
	 */
	define( 'AIOW_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'AIOW_PLUGINS_BASENAME' ) ) {
	/**
	 * AIOW_PLUGINS_BASENAME.
	 *
	 * @since 1.0
	 */
	define( 'AIOW_PLUGINS_BASENAME', basename( __FILE__ ) );
}

if ( ! defined( 'AIOW_PLUGINS_BASENAME_DIRNAME' ) ) {
	/**
	 * AIOW_PLUGINS_BASENAME_DIRNAME.
	 *
	 * @since 1.0
	 */
	define( 'AIOW_PLUGINS_BASENAME_DIRNAME', basename( dirname( __FILE__ ) ) );
}

/**
 * Check if WooCommerce is active
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	// Put your plugin code here.
	return;
}

/**
 * Plugin textdomain.
 */
if ( ! function_exists( 'all_in_one_woocommerce_textdomain' ) ) {
	function all_in_one_woocommerce_textdomain() {
		load_plugin_textdomain( 'all-in-one-wc', false, AIOW_PLUGINS_BASENAME_DIRNAME . '/languages' );
	}
}
add_action( 'plugins_loaded', 'all_in_one_woocommerce_textdomain' );

/**
 * Plugin activation.
 */
if ( ! function_exists( 'all_in_one_woocommerce_activation' ) ) {

	function all_in_one_woocommerce_activation() {
		// Activate WooCommerce plguin.
		if( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( AIOW_BASENAME );
			// Display error message.
			wp_die( __( 'Please activate WooCommerce', 'all-in-one-wc' ), 'Plugin dependency check',
				array(
					'back_link' => true,
				)
			);
		}

	}
}
register_activation_hook( __FILE__, 'all_in_one_woocommerce_activation' );

/**
 * Plugin deactivation.
 */
if ( ! function_exists( 'all_in_one_woocommerce_deactivation' ) ) {
	function all_in_one_woocommerce_deactivation() {
		// Deactivation code here.
	}
}
register_deactivation_hook( __FILE__, 'all_in_one_woocommerce_deactivation' );

/**
 * Initialization class.
 */
if ( ! function_exists( 'all_in_one_woocommerce_init' ) ) {
	function all_in_one_woocommerce_init() {
		global $all_in_one_woocommerce;
		$all_in_one_woocommerce = new Main();
		$all_in_one_woocommerce->__init_modules();
	}
	add_action( 'plugins_loaded', 'all_in_one_woocommerce_init' );
}

/**
 * Load core functions.
 */
if ( ! function_exists( 'AIOW' ) ) {
	/**
	 * Get `Main` class object.
	 */
	function AIOW() {
		return Main::instance();
	}
}
AIOW();
