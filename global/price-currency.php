<?php
/**
 * Price and Currency - Functions
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_price' ) ) {
	/**
	 * Product price.
	 *
	 * @param string     $price Product price.
	 * @param string     $currency currency.
	 * @param string     $hide_currency currency.
	 * @param array|null $args Argument.
	 *
	 * @return string
	 */
	function aiow_price( $price, $currency, $hide_currency, $args = null ) {
		$args = wp_parse_args( $args, array(
			'currency'          => 'DISABLED',
			'add_html_on_price' => true
		) );
		if ( 'yes' !== $hide_currency ) {
			$args['currency'] = $currency;
		}
		return wc_price( $price, $args );
	}
}

if ( ! function_exists( 'aiow_get_woocommerce_currencies_and_symbols' ) ) {
	/**
	 * Get woocommerce currencies and symbols.
	 *
	 * @return string
	 */
	function aiow_get_woocommerce_currencies_and_symbols() {
		$currencies_and_symbols = get_woocommerce_currencies();
		foreach ( $currencies_and_symbols as $code => $name ) {
			$currencies_and_symbols[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}
		return $currencies_and_symbols;
	}
}
