<?php
/**
 * All In One For WooCommerce - Module - Free Price
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Price;

if ( ! class_exists( 'Free_Price' ) ) {

	/**
	 * Declare `Free_Price` class extends `\AIOW\Modules\Register_Modules`
	 */
	class Free_Price extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->id         = 'free_price';
			$this->short_desc = __( 'Free Price Labels', 'all-in-one-wc' );
			$this->desc       = __( 'Set free price labels (Variable products allowed in Free version).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set free price labels.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-free-price-labels';
			parent::__construct();

			if ( $this->is_enabled() ) {
				if ( AIOW_IS_WC_VERSION_BELOW_3 ) {
					add_filter( 'woocommerce_free_price_html',           array( $this, 'modify_free_price_simple_external_custom' ), PHP_INT_MAX, 2 );
					add_filter( 'woocommerce_grouped_free_price_html',   array( $this, 'modify_free_price_grouped' ),                PHP_INT_MAX, 2 );
					add_filter( 'woocommerce_variable_free_price_html',  array( $this, 'modify_free_price_variable' ),               PHP_INT_MAX, 2 );
					add_filter( 'woocommerce_variation_free_price_html', array( $this, 'modify_free_price_variation' ),              PHP_INT_MAX, 2 );
				} else {
					add_filter( 'woocommerce_get_price_html',            array( $this, 'maybe_modify_price' ),                       PHP_INT_MAX, 2 );
				}
			}
		}

		/**
		 * All price free.
		 *
		 * @param object $_product Product object.
		 * @param string $type Product type.
		 * @return string
		 */
		function are_all_prices_free( $_product, $type ) {
			if ( 'variable' === $type ) {
				$prices    = $_product->get_variation_prices( true );
				$min_price = current( $prices['price'] );
				$max_price = end( $prices['price'] );
				if ( '' !== $min_price && '' !== $max_price ) {
					return ( 0 == $min_price && 0 == $max_price );
				}
			} elseif ( 'variable' === $type ) {
				$child_prices     = array();
				foreach ( $_product->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( '' !== $child->get_price() ) {
						$child_prices[] = aiow_get_product_display_price( $child );
					}
				}
				if ( ! empty( $child_prices ) ) {
					$min_price = min( $child_prices );
					$max_price = max( $child_prices );
				} else {
					$min_price = '';
					$max_price = '';
				}
				if ( '' !== $min_price && '' !== $max_price ) {
					return ( 0 == $min_price && 0 == $max_price );
				}
			}
			return false;
		}

		/**
		 * Maybe modify price.
		 *
		 * @param string $price Price.
		 * @param object $_product Product Object.
		 * @return string.
		 */
		function maybe_modify_price( $price, $_product ) {
			if ( '' !== $price ) {
				if ( 0 == $_product->get_price() ) {
					if ( $_product->is_type( 'grouped' ) ) {
						return ( $this->are_all_prices_free( $_product, 'grouped' ) )  ? $this->modify_free_price_grouped( $price, $_product )  : $price;
					} elseif ( $_product->is_type( 'variable' ) ) {
						return ( $this->are_all_prices_free( $_product, 'variable' ) ) ? $this->modify_free_price_variable( $price, $_product ) : $price;
					} elseif ( $_product->is_type( 'variation' ) ) {
						return $this->modify_free_price_variation( $price, $_product );
					} else {
						return $this->modify_free_price_simple_external_custom( $price, $_product );
					}
				}
			}
			return $price;
		}

		/**
		 * Get view ID.
		 *
		 * @param int $product_id Product ID.
		 * @return string
		 */
		function get_view_id( $product_id ) {
			$view = 'single';
			if ( is_single( $product_id ) ) {
				$view = 'single';
			} elseif ( is_single() ) {
				$view = 'related';
			} elseif ( is_front_page() ) {
				$view = 'home';
			} elseif ( is_page() ) {
				$view = 'page';
			} elseif ( is_archive() ) {
				$view = 'archive';
			}
			return $view;
		}

		/**
		 * Modify free price.
		 *
		 * @param string $price Price.
		 * @param object $_product Product.
		 * @return string
		 */
		function modify_free_price_simple_external_custom( $price, $_product ) {
			$default = '<span class="amount">' . __( 'Free!', 'woocommerce' ) . '</span>';
			return ( $_product->is_type( 'external' ) ) ?
				do_shortcode( aiow_option( 'aiow_free_price_external_' . $this->get_view_id( aiow_get_product_id_or_variation_parent_id( $_product ) ), $default ) ) :
				do_shortcode( aiow_option( 'aiow_free_price_simple_'   . $this->get_view_id( aiow_get_product_id_or_variation_parent_id( $_product ) ), $default ) );
		}

		/**
		 * Modify free price group.
		 *
		 * @param string $price Price.
		 * @param object $_product Product.
		 * @return string
		 */
		function modify_free_price_grouped( $price, $_product ) {
			return do_shortcode( aiow_option( 'aiow_free_price_grouped_' . $this->get_view_id( aiow_get_product_id_or_variation_parent_id( $_product ) ), __( 'Free!', 'woocommerce' ) ) );
		}

		/**
		 * Modify free variable price.
		 *
		 * @param string $price Price.
		 * @param object $_product Product.
		 * @return string
		 */
		function modify_free_price_variable( $price, $_product ) {
			return do_shortcode( apply_filters( 'aiow_option', __( 'Free!', 'woocommerce' ), aiow_option( 'aiow_free_price_variable_' . $this->get_view_id( aiow_get_product_id_or_variation_parent_id( $_product ) ), __( 'Free!', 'woocommerce' ) ) ) );
		}

		/**
		 * Modify free variation price.
		 *
		 * @param string $price Price.
		 * @param object $_product Product.
		 * @return string
		 */
		function modify_free_price_variation( $price, $_product ) {
			return do_shortcode( apply_filters( 'aiow_option', __( 'Free!', 'woocommerce' ), aiow_option( 'aiow_free_price_variable_variation', __( 'Free!', 'woocommerce' ) ) ) );
		}
	}
}
