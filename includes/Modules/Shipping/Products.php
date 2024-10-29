<?php
/**
 * Shipping Module - Shipping Methods by Products
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Products' ) ) {

	/**
	 * Declare class `Products` extends to `\AIOW\Modules\Condition`.
	 */
	class Products extends Condition {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_by_products';
			$this->short_desc = __( 'Shipping Methods by Products', 'all-in-one-wc' );
			$this->desc       = __( 'Set products, product categories, tags or shipping classes to include/exclude for shipping methods to show up (Free shipping available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set products, product categories, tags or shipping classes to include/exclude for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-products';

			$this->condition_options = array(
				'products' => array(
					'title' => __( 'Products', 'all-in-one-wc' ),
					'desc'  => __( 'Shipping methods by <strong>products</strong>.', 'all-in-one-wc' ),
				),
				'product_cats' => array(
					'title' => __( 'Product Categories', 'all-in-one-wc' ),
					'desc'  => __( 'Shipping methods by <strong>products categories</strong>.', 'all-in-one-wc' ),
				),
				'product_tags' => array(
					'title' => __( 'Product Tags', 'all-in-one-wc' ),
					'desc'  => __( 'Shipping methods by <strong>products tags</strong>.', 'all-in-one-wc' ),
				),
				'classes' => array(
					'title' => __( 'Product Shipping Classes', 'all-in-one-wc' ),
					'desc'  => '',
				),
			);

			parent::__construct();
		}

		/**
		 * Condition for check data.
		 *
		 * @param bool  $cart_instead_of_package Cart.
		 * @param array $package Packages.
		 * @return bool
		 */
		function check_for_data( $cart_instead_of_package, $package ) {
			if ( $cart_instead_of_package ) {
				if ( ! isset( WC()->cart ) || WC()->cart->is_empty() ) {
					return false;
				}
			} else {
				if ( ! isset( $package['contents'] ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Get items.
		 *
		 * @param bool  $cart_instead_of_package Cart.
		 * @param array $package Packages.
		 * @return bool
		 */
		function get_items( $cart_instead_of_package, $package ) {
			return ( $cart_instead_of_package ? WC()->cart->get_cart() : $package['contents'] );
		}

		/**
		 * Check condition.
		 *
		 * @param string $options_id Option ID.
		 * @param array  $values values.
		 * @param bool   $include_or_exclude Include OR exclude.
		 * @param array  $package Packages.
		 * @return bool
		 */
		function check( $options_id, $values, $include_or_exclude, $package ) {
			$cart_instead_of_package = ( 'yes' === aiow_option( 'aiow_shipping_by_' . $options_id . '_cart_not_package', 'yes' ) );
			if ( ! $this->check_for_data( $cart_instead_of_package, $package ) ) {
				return true;
			}
			if ( 'products' === $options_id && ( $do_add_variations = ( 'yes' === aiow_option( 'aiow_shipping_by_' . $options_id . '_add_variations_enabled', 'no' ) ) ) ) {
				$products_variations = array();
				foreach ( $values as $_product_id ) {
					$_product = wc_get_product( $_product_id );
					if ( $_product->is_type( 'variable' ) ) {
						$products_variations = array_merge( $products_variations, $_product->get_children() );
					} else {
						$products_variations[] = $_product_id;
					}
				}
				$values = array_unique( $products_variations );
			}
			$validate_all_for_include = ( 'include' === $include_or_exclude && 'yes' === aiow_option( 'aiow_shipping_by_' . $options_id . '_validate_all_enabled', 'no' ) );
			foreach ( $this->get_items( $cart_instead_of_package, $package ) as $item ) {
				switch( $options_id ) {
					case 'products':
						$_product_id = ( $do_add_variations && 0 != $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
						if ( $validate_all_for_include && ! in_array( $_product_id, $values ) ) {
							return false;
						} elseif ( ! $validate_all_for_include && in_array( $_product_id, $values ) ) {
							return true;
						}
						break;
					case 'product_cats':
					case 'product_tags':
						$product_terms = get_the_terms( $item['product_id'], ( 'product_cats' === $options_id ? 'product_cat' : 'product_tag' ) );
						if ( empty( $product_terms ) ) {
							if ( $validate_all_for_include ) {
								return false;
							} else {
								break;
							}
						}
						foreach( $product_terms as $product_term ) {
							if ( $validate_all_for_include && ! in_array( $product_term->term_id, $values ) ) {
								return false;
							} elseif ( ! $validate_all_for_include && in_array( $product_term->term_id, $values ) ) {
								return true;
							}
						}
						break;
					case 'classes':
						$product = $item['data'];
						$product_shipping_class = $product->get_shipping_class_id();
						if ( $validate_all_for_include && ! in_array( $product_shipping_class, $values ) ) {
							return false;
						} elseif ( ! $validate_all_for_include && in_array( $product_shipping_class, $values ) ) {
							return true;
						}
						break;
				}
			}
			return $validate_all_for_include;
		}

		/**
		 * Get condition option.
		 *
		 * @param string $options_id Option ID.
		 * @return string
		 */
		function get_condition_options( $options_id ) {
			switch( $options_id ) {
				case 'products':
					return aiow_get_products( array(), 'any', 1024, ( 'yes' === aiow_option( 'aiow_shipping_by_' . $options_id . '_add_variations_enabled', 'no' ) ) );
				case 'product_cats':
					return aiow_get_terms( 'product_cat' );
				case 'product_tags':
					return aiow_get_terms( 'product_tag' );
				case 'classes':
					$wc_shipping              = \WC_Shipping::instance();
					$shipping_classes_terms   = $wc_shipping->get_shipping_classes();
					$shipping_classes_options = array( 0 => __( 'No shipping class', 'woocommerce' ) );
					foreach ( $shipping_classes_terms as $shipping_classes_term ) {
						$shipping_classes_options[ $shipping_classes_term->term_id ] = $shipping_classes_term->name;
					}
					return $shipping_classes_options;
			}
		}

		/**
		 * Get additional section settings.
		 *
		 * @param string $options_id Option ID.
		 * @return array
		 */
		function get_additional_section_settings( $options_id ) {
			$return = array(
				array(
					'title'    => __( '"Include" Options', 'all-in-one-wc' ),
					'desc_tip' => __( 'Enable this checkbox if you want all products in cart to be valid (instead of at least one).', 'all-in-one-wc' ),
					'desc'     => __( 'Validate all', 'all-in-one-wc' ),
					'id'       => 'aiow_shipping_by_' . $options_id . '_validate_all_enabled',
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'title'    => __( 'Cart instead of Package', 'all-in-one-wc' ),
					'desc_tip' => __( 'Enable this checkbox if you want to check all cart products instead of package.', 'all-in-one-wc' ),
					'desc'     => __( 'Enable', 'all-in-one-wc' ),
					'id'       => 'aiow_shipping_by_' . $options_id . '_cart_not_package',
					'type'     => 'checkbox',
					'default'  => 'yes',
				),
			);
			if ( 'products' === $options_id ) {
				$return[] = array(
					'title'    => __( 'Add Products Variations', 'all-in-one-wc' ),
					'desc_tip' => __( 'Enable this checkbox if you want to add products variations to the products list.', 'all-in-one-wc' ) . ' ' .
						__( 'Save changes after enabling this option.', 'all-in-one-wc' ),
					'desc'     => __( 'Add', 'all-in-one-wc' ),
					'id'       => 'aiow_shipping_by_' . $options_id . '_add_variations_enabled',
					'type'     => 'checkbox',
					'default'  => 'no',
				);
			}
			return $return;
		}
	}
}
