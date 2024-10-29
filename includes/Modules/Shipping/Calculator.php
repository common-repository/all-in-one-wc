<?php
/**
 * Shipping Module - Shipping Calculator.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Calculator' ) ) {

	/**
	 * Declare class `Calculator` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Calculator extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id         = 'shipping_calculator';
			$this->short_desc = __( 'Shipping Calculator', 'all-in-one-wc' );
			$this->desc       = __( 'Customize WooCommerce shipping calculator on cart page. Calculate shipping label (Plus). Update totals label (Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Customize WooCommerce shipping calculator on cart page.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-calculator-customizer';
			parent::__construct();

			if ( $this->is_enabled() ) {
				add_filter( 'woocommerce_shipping_calculator_enable_city' ,    array( $this, 'enable_city' ) );
				add_filter( 'woocommerce_shipping_calculator_enable_postcode', array( $this, 'enable_postcode' ) );
				add_action( 'wp_head', array( $this, 'add_custom_styles' ) );
				if ( 'yes' === aiow_option( 'aiow_shipping_calculator_labels_enabled', 'no' ) ) {
					add_action( 'wp_enqueue_scripts', array( $this, 'change_labels' ) );
				}
			}
		}

		/**
		 * Change labels.
		 */
		function change_labels() {
			if ( function_exists( 'is_cart' ) && is_cart() ) {
				wp_enqueue_style(   'aiow-calculator', aiow_plugin_url() . '/assets/css/calculator.css', array(), AIOW()->version );
				wp_enqueue_script(  'aiow-calculator-js', aiow_plugin_url() . '/assets/js/calculator.js', array( 'jquery' ), AIOW()->version, true );
				wp_localize_script( 'aiow-calculator-js', 'aiow_object', array(
					'calculate_shipping_label' => aiow_option( 'aiow_shipping_calculator_label_calculate_shipping', '' ),
					'update_totals_label'      => aiow_option( 'aiow_shipping_calculator_label_update_totals', '' ),
				) );
			}
		}

		/**
		 * Add custom styles.
		 */
		function add_custom_styles() {
			$html = '<style type="text/css">';
			if ( 'no' === aiow_option( 'aiow_shipping_calculator_enable_state' ) ) {
				$html .= '#calc_shipping_state { display: none !important; }';
			}
			if ( 'yes' === aiow_option( 'aiow_shipping_calculator_enable_force_block_open' ) ) {
				$html .= '.shipping-calculator-form { display: block !important; }';
				if ( 'hide' === aiow_option( 'aiow_shipping_calculator_enable_force_block_open_button' ) ) {
					$html .= 'a.shipping-calculator-button { display: none !important; }';
				} elseif ( 'noclick' === aiow_option( 'aiow_shipping_calculator_enable_force_block_open_button' ) ) {
					$html .= 'a.shipping-calculator-button { pointer-events: none; cursor: default; }';
				}
			}
			$html .= '</style>';
			echo $html;
		}

		/**
		 * Enable city.
		 *
		 * @return bool
		 */
		function enable_city() {
			return ( 'yes' === aiow_option( 'aiow_shipping_calculator_enable_city' ) );
		}

		/**
		 * Enable postcode.
		 *
		 * @return bool
		 */
		function enable_postcode() {
			return ( 'yes' === aiow_option( 'aiow_shipping_calculator_enable_postcode' ) );
		}
	}
}
