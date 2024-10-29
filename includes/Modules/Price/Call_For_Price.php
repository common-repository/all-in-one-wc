<?php
/**
 * All In One For WooCommerce - Module - Call for Price
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Price;

if ( ! class_exists( 'Call_For_Price' ) ) {

	/**
	 * Declare `Call_For_Price` extends `\AIOW\Modules\Register_Modules`
	 */
	class Call_For_Price extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->id         = 'call_for_price';
			$this->short_desc = __( 'Call for Price', 'all-in-one-wc' );
			$this->desc       = __( 'Create any custom price label for all products with empty price.', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Create any custom price label for all products with empty price.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-call-for-price';
			parent::__construct();

			if ( $this->is_enabled() ) {
				add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'get_variation_prices_hash' ),                 PHP_INT_MAX, 3 );
				add_action( 'init',                                  array( $this, 'add_empty_price_hooks' ),                     PHP_INT_MAX );
				add_filter( 'woocommerce_sale_flash',                array( $this, 'hide_sales_flash' ),                          PHP_INT_MAX, 3 );
				add_action( 'admin_head',                            array( $this, 'hide_variation_price_required_placeholder' ), PHP_INT_MAX );
				add_filter( 'woocommerce_variation_is_visible',      array( $this, 'make_variation_visible_with_empty_price' ),   PHP_INT_MAX, 4 );
				add_action( 'wp_head',                               array( $this, 'hide_disabled_variation_add_to_cart_button' ) );
				if ( 'yes' === aiow_option( 'aiow_call_for_price_make_all_empty', 'no' ) ) {
					add_filter( AIOW_PRODUCT_GET_PRICE_FILTER,                  array( $this, 'make_empty_price' ), PHP_INT_MAX, 2 );
					add_filter( 'woocommerce_variation_prices_price',          array( $this, 'make_empty_price' ), PHP_INT_MAX, 2 );
					if ( ! AIOW_IS_WC_VERSION_BELOW_3 ) {
						add_filter( 'woocommerce_product_variation_get_price', array( $this, 'make_empty_price' ), PHP_INT_MAX, 2 );
					}
				}
			}
		}

		/**
		 * Get variatio price by price hash.
		 *
		 * @param array  $price_hash Price hash.
		 * @param object $_product Product Object.
		 * @param object $display Display OR not.
		 * @return array
		 */
		function get_variation_prices_hash( $price_hash, $_product, $display ) {
			$price_hash['aiow_call_for_price'] = array(
				get_option( 'aiow_call_for_price_make_all_empty', 'no' ),
			);
			return $price_hash;
		}

		/**
		 * Make variation visible with empty price.
		 *
		 * @param bool     $visible Is visible.
		 * @param int      $_variation_id variation ID.
		 * @param int      $_id ID.
		 * @param object   $_product Product object.
		 * @return bool
		 */
		function make_variation_visible_with_empty_price( $visible, $_variation_id, $_id, $_product ) {
			if ( '' === $_product->get_price() ) {
				$visible = true;
				// Published == enabled checkbox
				if ( get_post_status( $_variation_id ) != 'publish' ) {
					$visible = false;
				}
			}
			return $visible;
		}

		/**
		 * Hide variation.
		 */
		function hide_disabled_variation_add_to_cart_button() {
			echo '<style>div.woocommerce-variation-add-to-cart-disabled { display: none ! important; }</style>';
		}

		/**
		 * Hide variation required placeholder.
		 */
		function hide_variation_price_required_placeholder() {
			echo '<style>
				div.variable_pricing input.wc_input_price::-webkit-input-placeholder { /* WebKit browsers */
					color: transparent;
				}
				div.variable_pricing input.wc_input_price:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
					color: transparent;
				}
				div.variable_pricing input.wc_input_price::-moz-placeholder { /* Mozilla Firefox 19+ */
					color: transparent;
				}
				div.variable_pricing input.wc_input_price:-ms-input-placeholder { /* Internet Explorer 10+ */
					color: transparent;
				}
			</style>';
		}

		/**
		 * Make empty price.
		 *
		 * @param string $price Price.
		 * @param object $_product Product object.
		 * @return string
		 */
		function make_empty_price( $price, $_product ) {
			return '';
		}

		/**
		 * Add empty price.=
		 */
		function add_empty_price_hooks() {
			add_filter( 'woocommerce_empty_price_html',           array( $this, 'on_empty_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_variable_empty_price_html',  array( $this, 'on_empty_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_grouped_empty_price_html',   array( $this, 'on_empty_price' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_variation_empty_price_html', array( $this, 'on_empty_price' ), PHP_INT_MAX, 2 ); // Only in < WC3
		}

		/**
		 * Hide "sales" icon for empty price products.
		 *
		 * @param string $onsale_html Is onsale.
		 * @param object $post Post object.
		 * @param object $product Product data.
		 * @return string
		 */
		function hide_sales_flash( $onsale_html, $post, $product ) {
			if ( 'yes' === aiow_option( 'aiow_call_for_price_hide_sale_sign', 'yes' ) && '' === $product->get_price() ) {
				return '';
			}
			return $onsale_html;
		}

		/**
		 * On empty price filter - return the label.
		 *
		 * @param int    $price Price
		 * @param object $_product Product data.
		 * @return
		 */
		function on_empty_price( $price, $_product ) {
			if ( '' !== aiow_option( 'aiow_call_for_price_text_variation' ) && $_product->is_type( 'variation' ) ) {
				return do_shortcode( apply_filters( 'aiow_option', '<strong>Call for price</strong>', aiow_option( 'aiow_call_for_price_text_variation' ) ) );
			} elseif ( '' !== aiow_option( 'aiow_call_for_price_text' ) && is_single( get_the_ID() ) ) {
				return do_shortcode( apply_filters( 'aiow_option', '<strong>Call for price</strong>', aiow_option( 'aiow_call_for_price_text' ) ) );
			} elseif ( '' !== aiow_option( 'aiow_call_for_price_text_on_related' ) && is_single() && ! is_single( get_the_ID() ) ) {
				return do_shortcode( apply_filters( 'aiow_option', '<strong>Call for price</strong>', aiow_option( 'aiow_call_for_price_text_on_related' ) ) );
			} elseif ( '' !== aiow_option( 'aiow_call_for_price_text_on_archive' ) && is_archive() ) {
				return do_shortcode( apply_filters( 'aiow_option', '<strong>Call for price</strong>', aiow_option( 'aiow_call_for_price_text_on_archive' ) ) );
			} elseif ( '' !== aiow_option( 'aiow_call_for_price_text_on_home' ) && is_front_page() ) {
				return do_shortcode( apply_filters( 'aiow_option', '<strong>Call for price</strong>', aiow_option( 'aiow_call_for_price_text_on_home' ) ) );
			} else {
				return $price;
			}
		}
	}
}
