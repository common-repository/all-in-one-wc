<?php
/**
 * Register Order Items Shortcode
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Shortcodes;

if ( ! class_exists( 'Order_Items' ) ) {

	/**
	 * Declare class `Order_Items` extends `Shortcodes`
	 */
	class Order_Items extends Shortcodes {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->the_shortcodes = array(
				'aiow_order_items_table',
			);
			parent::__construct();
		}

		/**
		 * Extra attributes.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function add_extra_atts( $atts ) {
			$modified_atts = array_merge( array(
				'order_id'                            => ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID(),
				'hide_currency'                       => 'no',
				'table_class'                         => '',
				'shipping_as_item'                    => '',
				'discount_as_item'                    => '',
				'columns'                             => '',
				'columns_titles'                      => '',
				'columns_styles'                      => '',
				'tax_percent_format'                  => '%.2f %%',
				'item_image_width'                    => 0,
				'item_image_height'                   => 0,
				'product_image_width'                 => 0,
				'product_image_height'                => 0,
				'price_prefix'                        => '',
				'quantity_prefix'                     => '',
				'style_item_name_variation'           => 'font-size:smaller;',
				'variation_as_metadata'               => 'yes',
				'wc_extra_product_options_show_price' => 'no',
				'order_user_roles'                    => '',
				'exclude_by_categories'               => '',
				'exclude_by_tags'                     => '',
				'exclude_by_attribute__name'          => '',
				'exclude_by_attribute__value'         => '',
				'add_variation_info_to_item_name'     => 'yes',
				'insert_page_break'                   => '',
				'multiply_cost'                       => 1,
				'multiply_profit'                     => 1,
				'refunded_items_table'                => 'no',
				'hide_zero_prices'                    => 'no',
				'multicolumns_glue'                   => '<br>',
				'sort_by_column'                      => 0,
				'product_barcode_width'               => 60,
				'product_barcode_height'              => 60,
			), $atts );
			return $modified_atts;
		}

		/**
		 * Init all attribute.
		 *
		 * @param array $atts Attribute.
		 * @return array
		 */
		function init_atts( $atts ) {
			$this->the_order = ( 'shop_order' === get_post_type( $atts['order_id'] ) ) ? wc_get_order( $atts['order_id'] ) : null;
			if ( ! $this->the_order ) {
				return false;
			}
			if ( 0 != $atts['item_image_width'] ) {
				$atts['product_image_width'] = $atts['item_image_width'];
			}
			if ( 0 != $atts['item_image_height'] ) {
				$atts['product_image_height'] = $atts['item_image_height'];
			}
			return $atts;
		}

		/**
		 * Extra Check.
		 *
		 * @param array $atts Attributes.
		 * @return bool
		 */
		function extra_check( $atts ) {
			if ( '' != $atts['order_user_roles'] ) {
				$user_info           = get_userdata( $this->the_order->customer_user );
				$user_roles          = $user_info->roles;
				$user_roles_to_check = explode( ',', $atts['order_user_roles'] );
				foreach ( $user_roles_to_check as $user_role_to_check ) {
					if ( in_array( $user_role_to_check, $user_roles ) ) {
						return true;
					}
				}
				return false;
			}
			return true;
		}

		/**
		 * Price shortcode.
		 *
		 * @param string $raw_price Raw price.
		 * @param array  $atts Attributes.
		 * @return string
		 */
		private function aiow_price_shortcode( $raw_price, $atts ) {
			if ( 'yes' === $atts['hide_zero_prices'] && 0 == $raw_price ) {
				return '';
			}
			return aiow_price( $atts['price_prefix'] . $raw_price, aiow_get_order_currency( $this->the_order ), $atts['hide_currency'] );
		}

		/**
		 * Add items.
		 *
		 * @param array $items Items.
		 * @param array $new_item_args  New items.
		 * @return array
		 */
		private function add_item( $items, $new_item_args = array() ) {
			if ( empty ( $new_item_args ) ) {
				return $items;
			}
			extract( $new_item_args );
			// Create item
			if ( AIOW_IS_WC_VERSION_BELOW_3 ) {
				$item = array(
					'is_custom'         => true,
					'name'              => $name,
					'type'              => 'line_item',
					'qty'               => $qty,
					'line_subtotal'     => $line_subtotal,
					'line_total'        => $line_total,
					'line_tax'          => $line_tax,
					'line_subtotal_tax' => $line_subtotal_tax,
					'item_meta'         => array(
						'_qty'               => array( $qty ),
						'_line_subtotal'     => array( $line_subtotal ),
						'_line_total'        => array( $line_total ),
						'_line_tax'          => array( $line_tax ),
						'_line_subtotal_tax' => array( $line_subtotal_tax ),
					),
				);
			} else {
				if ( 'shipping' === $type ) {
					$item = new WC_Order_Item_Shipping();
					$item->set_props( array(
						'method_title' => $name,
						'method_id'    => '',
						'total'        => wc_format_decimal( $line_total ),
						'total_tax'    => $line_tax,
						'taxes'        => array(
							'total' => array( $line_tax ),
						),
					) );
				} else { // ( 'discount' === $type )
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
						'name'         => $name,
						'total'        => wc_format_decimal( $line_total ),
						'total_tax'    => $line_tax,
						'tax_class'    => '',
						'tax_status'   => 'taxable',
						'taxes'        => array(
							'total' => array( $line_tax ),
						),
					) );
				}
			}
			$items[] = $item;
			return $items;
		}

		/**
		 * Get tax class.
		 *
		 * @param string $tax_class Tax class name.
		 * @return bool
		 */
		function get_tax_class_name( $tax_class ) {
			$tax_classes       = WC_Tax::get_tax_classes();
			$classes_names     = array();
			$classes_names[''] = __( 'Standard', 'woocommerce' );
			if ( ! empty( $tax_classes ) ) {
				foreach ( $tax_classes as $class ) {
					$classes_names[ sanitize_title( $class ) ] = esc_html( $class );
				}
			}
			return ( isset( $classes_names[ $tax_class ] ) ) ? $classes_names[ $tax_class ] : '';
		}

		/**
		 * Get meta info.
		 *
		 * @param string $item_id Item ID.
		 * @param mixed  $the_product Product.
		 * @return string
		 */
		function get_meta_info( $item_id, $the_product ) {
			$meta_info = '';
			if ( $metadata = $this->the_order->has_meta( $item_id ) ) {
				$meta_info = array();
				foreach ( $metadata as $meta ) {

					// Skip hidden core fields
					if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
						'_qty',
						'_tax_class',
						'_product_id',
						'_variation_id',
						'_line_subtotal',
						'_line_subtotal_tax',
						'_line_total',
						'_line_tax',
						'method_id',
						'cost'
					) ) ) ) {
						continue;
					}

					// Skip serialised meta
					if ( is_serialized( $meta['meta_value'] ) ) {
						continue;
					}

					// Get attribute data
					if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
						$term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
						$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
						$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
					} else {
						$meta['meta_key']   = ( is_object( $the_product ) ) ? wc_attribute_label( $meta['meta_key'], $the_product ) : $meta['meta_key'];
					}
					$meta_info[] = wp_kses_post( rawurldecode( $meta['meta_key'] ) ) . ': ' . wp_kses_post( rawurldecode( $meta['meta_value'] ) );
				}
				$meta_info = implode( ', ', $meta_info );
			}
			return $meta_info;
		}

		/**
		 * Order item table.
		 *
		 * @param array  $atts Attibutes.
		 * @param string $content Content.
		 * @return string
		 */
		function aiow_order_items_table( $atts, $content = '' ) {

			$the_order = $this->the_order;

			// Get columns
			$columns = explode( '|', $atts['columns'] );
			if ( empty( $columns ) ) {
				return '';
			}

			// Column titles and styles
			$columns_titles = ( '' == $atts['columns_titles'] ) ? array() : explode( '|', $atts['columns_titles'] );
			$columns_styles = ( '' == $atts['columns_styles'] ) ? array() : explode( '|', $atts['columns_styles'] );

			// The Items
			$the_items = array();
			if ( 'yes' === $atts['refunded_items_table'] ) {
				foreach ( $this->the_order->get_refunds() as $_refund ) {
					$the_items = array_merge( $the_items, $_refund->get_items() );
				}
			} else {
				$the_items = $the_order->get_items();
			}

			// Shipping as item
			if ( '' != $atts['shipping_as_item'] && $the_order->get_total_shipping() > 0 ) {
				$name                    = str_replace( '%shipping_method_name%', $the_order->get_shipping_method(), $atts['shipping_as_item'] );
				$total_shipping_tax_excl = $the_order->get_total_shipping();
				$shipping_tax            = $the_order->get_shipping_tax();
				$the_items = $this->add_item( $the_items, array(
					'name'              => $name,
					'qty'               => 1,
					'line_subtotal'     => $total_shipping_tax_excl,
					'line_total'        => $total_shipping_tax_excl,
					'line_tax'          => $shipping_tax,
					'line_subtotal_tax' => $shipping_tax,
					'type'              => 'shipping',
				) );
			}

			// Discount as item
			if ( '' != $atts['discount_as_item'] && $the_order->get_total_discount( true ) > 0 ) {
				$name                    = $atts['discount_as_item'];
				$total_discount_tax_excl = $the_order->get_total_discount( true );
				$discount_tax            = $the_order->get_total_discount( false ) - $total_discount_tax_excl;
				$total_discount_tax_excl *= -1;
				$discount_tax            *= -1;
				$the_items = $this->add_item( $the_items, array(
					'name'              => $name,
					'qty'               => 1,
					'line_subtotal'     => $total_discount_tax_excl,
					'line_total'        => $total_discount_tax_excl,
					'line_tax'          => $discount_tax,
					'line_subtotal_tax' => $discount_tax,
					'type'              => 'discount'
				) );
			}

			// Items to data[]
			$data = array();
			$item_counter = 0;
			foreach ( $the_items as $item_id => $item ) {
				$item['is_custom'] = ( isset( $item['is_custom'] ) );
				$the_product = ( true === $item['is_custom'] ) ? null : ( method_exists( $item, 'get_product' ) ? $item->get_product( $item ) : null );
				// Check if it's not excluded by category
				if ( '' != $atts['exclude_by_categories'] && $the_product ) {
					if ( aiow_product_has_terms( $the_product, $atts['exclude_by_categories'], 'product_cat' ) ) {
						continue;
					}
				}
				// Check if it's not excluded by tag
				if ( '' != $atts['exclude_by_tags'] && $the_product ) {
					if ( aiow_product_has_terms( $the_product, $atts['exclude_by_tags'], 'product_tag' ) ) {
						continue;
					}
				}
				// Check if it's not excluded by product attribute
				if ( $the_product && '' != $atts['exclude_by_attribute__name'] ) {
					$product_attributes = $the_product->get_attributes();
					if ( isset( $product_attributes[ $atts['exclude_by_attribute__name'] ] ) ) {
						$product_attribute = $product_attributes[ $atts['exclude_by_attribute__name'] ];
						if ( is_object( $product_attribute ) ) {
							if ( 'WC_Product_Attribute' === get_class( $product_attribute ) && in_array( $atts['exclude_by_attribute__value'], $product_attribute->get_options() ) ) {
								continue;
							}
						} elseif ( $atts['exclude_by_attribute__value'] === $product_attribute ) {
							continue;
						}
					}
				}
				$item_counter++;
				// Columns
				foreach( $columns as $cell_columns ) {
					$cell_columns = explode( ',', $cell_columns );
					$cell_data    = array();
					foreach ( $cell_columns as $column ) {
						$column_param = '';
						if ( false !== ( $pos = strpos( $column, '=' ) ) ) {
							$column_param = substr( $column, $pos + 1 );
							$column       = substr( $column, 0, $pos );
						}
						$column_cell_data = $this->get_cell( $column, $column_param, $atts, $the_order, $columns, $item_counter, $item_id, $item, $the_product );
						$column_cell_data = apply_filters( 'aiow_pdf_invoicing_cell_data', $column_cell_data, array(
							'column'       => $column,
							'column_param' => $column_param,
							'item'         => $item,
							'item_id'      => $item_id,
							'item_counter' => $item_counter,
							'product'      => $the_product,
							'order'        => $this->the_order,
						) );
						if ( '' !== $column_cell_data ) {
							$cell_data[] = $column_cell_data;
						}
					}
					$cell_data = ( empty( $cell_data ) ? '' : implode( $atts['multicolumns_glue'], $cell_data ) );
					$data[ $item_counter ][] = $cell_data;
				}
			}

			if ( empty( $data ) ) {
				return '';
			}

			// Sorting
			if ( $atts['sort_by_column'] > 0 ) {
				$this->sort_by_column = $atts['sort_by_column'];
				usort( $data, array( $this, 'sort_data' ) );
			}

			// Final filter
			$data = apply_filters( 'aiow_order_items_table_data', $data );

			// Final HTML
			$html = '';
			$table_html_args = array(
				'table_class'        => $atts['table_class'],
				'table_heading_type' => 'horizontal',
				'columns_classes'    => array(),
				'columns_styles'     => $columns_styles,
			);
			if ( '' != $atts['insert_page_break'] ) {
				$page_breaks  = explode ( '|', $atts['insert_page_break'] );
				$data_size    = count( $data );
				$slice_offset = 0;
				$html         = '';
				$slices       = 0;
				while ( $slice_offset < $data_size ) {
					if ( 0 != $slice_offset ) {
						$html .= '<tcpdf method="AddPage" />';
					}
					if ( isset( $page_breaks[ $slices ] ) ) {
						$current_page_break = $page_breaks[ $slices ];
					}
					$data_slice = array_slice( $data, $slice_offset, $current_page_break );
					$html .= aiow_get_table_html( array_merge( array( $columns_titles ), $data_slice ), $table_html_args );
					$slice_offset += $current_page_break;
					$slices++;
				}
			} else {
				$html = aiow_get_table_html( array_merge( array( $columns_titles ), $data ), $table_html_args );
			}

			return $html;
		}

		/**
		 * Reorder data.
		 *
		 * @param array $a Array.
		 * @param array $b Array.
		 * @return array
		 */
		function sort_data( $a, $b ) {
			$key = ( $this->sort_by_column - 1 );
			if ( ! isset( $a[ $key ] ) || ! isset( $b[ $key ] ) ) {
				return 0;
			}
			if ( $a[ $key ] == $b[ $key ] ) {
				return 0;
			}
			return ( $a[ $key ] < $b[ $key ] ) ? -1 : 1;
		}

		/**
		 * Get product item name.
		 *
		 * @param array $item Product.
		 * @return string
		 */
		function get_product_item_name( $item ) {
			$item_name = $item['name'];
			if ( 'yes' == aiow_option( 'aiow_pdf_invoicing_advanced_item_name_as_prod_title_enable', 'no' ) ) {
				if (
					'yes' == aiow_option( 'aiow_pdf_invoicing_advanced_item_name_as_prod_title_wpml', 'no' )
					&& function_exists( 'icl_object_id' )
				) {
					$item_name = get_the_title( icl_object_id( $item->get_product_ID(), 'product', false, ICL_LANGUAGE_CODE ) );
				} else {
					$item_name = get_the_title( $item->get_product_ID() );
				}
			}
			return $item_name;
		}

		/**
		 * Get cell.
		 *
		 * @param string     $column column name.
		 * @param string     $column_param column param.
		 * @param array      $atts Attributes.
		 * @param array      $the_order Order data.
		 * @param array      $columns All columns.
		 * @param string|int $item_counter Item count.
		 * @param string|int $item_id Item ID.
		 * @param array      $item Item data.
		 * @param array      $the_product Product data.
		 * @return mixed
		 */
		function get_cell( $column, $column_param, $atts, $the_order, $columns, $item_counter, $item_id, $item, $the_product ) {

			switch ( $column ) {

				case 'item_debug':
				case 'debug':
					return print_r( $item, true );

				case 'item_regular_price':
				case 'product_regular_price':
					return ( is_object( $the_product ) ) ? $this->aiow_price_shortcode( $the_product->get_regular_price(), $atts ) : '';

				case 'item_sale_price':
				case 'product_sale_price':
					return ( is_object( $the_product ) ) ? $this->aiow_price_shortcode( $the_product->get_sale_price(), $atts ) : '';

				case 'item_regular_price_multiply_qty':
				case 'product_regular_price_multiply_qty':
					return ( is_object( $the_product ) ) ? $this->aiow_price_shortcode( $the_product->get_regular_price() * $item['qty'], $atts ) : '';

				case 'item_sale_price_multiply_qty':
				case 'product_sale_price_multiply_qty':
					return ( is_object( $the_product ) ) ? $this->aiow_price_shortcode( $the_product->get_sale_price() * $item['qty'], $atts ) : '';

				case 'product_categories':
					return ( is_object( $the_product ) ) ?
						strip_tags( ( AIOW_IS_WC_VERSION_BELOW_3 ? $the_product->get_categories() : wc_get_product_category_list( $item['product_id'] ) ) ) :
						'';

				case 'item_tax_class':
				case 'tax_class':
					return ( isset( $item['tax_class'] ) ) ? $this->get_tax_class_name( $item['tax_class'] ) : '';

				case 'item_number':
					return $item_counter;

				case 'item_meta':
					return aiow_get_order_item_meta_info( $item_id, $item, $this->the_order, false, $the_product, array( 'is_custom' ) );

				case 'item_name':
				case 'product_name': // "product_" because of possible variation
					if ( true === $item['is_custom'] ) {
						return $this->get_product_item_name( $item );
					} else {
						$the_item_title = $this->get_product_item_name( $item );
						// Variation (if needed)
						if ( 'yes' === $atts['add_variation_info_to_item_name'] && isset( $item['variation_id'] ) && 0 != $item['variation_id'] && ! in_array( 'item_variation', $columns ) ) {
							$the_item_title .= '<div style="' . $atts['style_item_name_variation'] . '">';
							if ( 'yes' === $atts['variation_as_metadata'] ) {
								$the_item_title .= aiow_get_order_item_meta_info( $item_id, $item, $this->the_order, true, $the_product );
							} elseif ( is_object( $the_product ) && $the_product->is_type( 'variation' ) ) {
								$the_item_title .= str_replace( 'pa_', '', urldecode( aiow_get_product_formatted_variation( $the_product, true ) ) );
							}
							$the_item_title .= '</div>';
						}
						// "WooCommerce TM Extra Product Options" plugin options
						if ( 'yes' === aiow_option( 'aiow_general_advanced_wcepo_enable', 'yes' ) ) {
							$tmcartepo_data = ( AIOW_IS_WC_VERSION_BELOW_3 ?
								( isset( $item['tmcartepo_data'] ) ? maybe_unserialize( $item['tmcartepo_data'] ) : '' ) :
								$item->get_meta( '_tmcartepo_data' )
							);
							if ( ! empty( $tmcartepo_data ) ) {
								$options_prices = array();
								foreach ( $tmcartepo_data as $option ) {
									$option_info = '';
									if ( isset( $option['name'] ) && '' != $option['name'] ) {
										$option_info .= $option['name'] . ': ';
									}
									if ( isset( $option['value'] ) && '' != $option['value'] ) {
										$option_info .= $option['value'];
									}
									if ( isset( $option['price'] ) && 'yes' === $atts['wc_extra_product_options_show_price'] ) {
										$option_info .= ( $option['price'] > 0 ) ? ' +' . wc_price( $option['price'] ) : ' ' . wc_price( $option['price'] );
									}
									if ( '' != $option_info ) {
										$options_prices[] = $option_info;
									}
								}
								$the_item_title .= '<div style="' . $atts['style_item_name_variation'] . '">' . implode( '<br>', $options_prices ) . '</div>';
							}
						}
						return $the_item_title;
					}

				case 'item_product_input_fields':
					return aiow_get_product_input_fields( $item );

				case 'item_product_input_fields_with_titles':
					return aiow_get_product_input_fields( $item, true, ( '' != $column_param ? $column_param : ', ' ) );

				case 'item_product_addons':
					return aiow_get_product_addons( $item, aiow_get_order_currency( $this->the_order ) );

				case 'item_key':
					if ( isset( $column_param ) && '' != $column_param && isset( $item[ $column_param ] ) ) {
						$maybe_unserialized_value = maybe_unserialize( $item[ $column_param ] );
						if ( is_array( $maybe_unserialized_value ) ) {
							return isset( $maybe_unserialized_value['name'] ) ? $maybe_unserialized_value['name'] : '';
						} else {
							return $maybe_unserialized_value;
						}
					} else {
						return '';
					}

				case 'item_attribute':
				case 'product_attribute':
					if ( isset( $column_param ) && '' != $column_param && is_object( $the_product ) ) {
						$product_attribute = $the_product->get_attribute( $column_param );
						if ( '' === $product_attribute && $the_product->is_type( 'variation' ) ) {
							$parent_product = wc_get_product( $the_product->get_parent_id() );
							$product_attribute = $parent_product->get_attribute( $column_param );
						}
						return $product_attribute;
					} else {
						return '';
					}

				case 'item_excerpt':
				case 'product_excerpt':
					if ( true === $item['is_custom'] || ! isset( $item['product_id'] ) ) {
						return '';
					} else {
						global $post;
						$post = get_post( $item['product_id'] );
						setup_postdata( $post );
						$the_excerpt = get_the_excerpt();
						wp_reset_postdata();
						return $the_excerpt;
					}

				case 'item_short_description':
				case 'product_short_description':
					return ( ! is_object( $the_product ) ) ? '' : ( AIOW_IS_WC_VERSION_BELOW_3 ? $the_product->post->post_excerpt : $the_product->get_short_description() );

				case 'item_variation':
				case 'product_variation':
					if ( 0 != $item['variation_id'] ) {
						if ( 'yes' === $atts['variation_as_metadata'] ) {
							return aiow_get_order_item_meta_info( $item_id, $item, $this->the_order, true, $the_product );
						} elseif ( is_object( $the_product ) && $the_product->is_type( 'variation' ) ) {
							return str_replace( 'pa_', '', urldecode( aiow_get_product_formatted_variation( $the_product, true ) ) );
						} else {
							return '';
						}
					} else {
						return '';
					}

				case 'item_thumbnail':
				case 'product_thumbnail':
					$image_id  = ( ! is_object( $the_product ) ) ? 0 : $the_product->get_image_id();
					$image_src = ( 0 != $image_id ) ? wp_get_attachment_image_src( $image_id ) : wc_placeholder_img_src();
					if ( is_array( $image_src ) ) {
						$image_src = $image_src[0];
					}
					$maybe_width  = ( 0 != $atts['product_image_width'] )  ? ' width="'  . $atts['product_image_width']  . '"' : '';
					$maybe_height = ( 0 != $atts['product_image_height'] ) ? ' height="' . $atts['product_image_height'] . '"' : '';
					return '<img src="' . $image_src . '"' . $maybe_width . $maybe_height . '>';

				case 'item_sku':
				case 'product_sku':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_sku();

				case 'item_quantity':
					return $atts['quantity_prefix'] . $item['qty'];

				case 'item_quantity_refunded':
					return ( ! $item['is_custom'] && $item_id ? $this->the_order->get_qty_refunded_for_item( $item_id ) : '' );

				case 'item_quantity_excl_refunded':
					return ( ! $item['is_custom'] && $item_id ? ( $item['qty'] + $this->the_order->get_qty_refunded_for_item( $item_id ) ) : '' );

				case 'item_total_refunded':
					return ( ! $item['is_custom'] && $item_id ? $this->aiow_price_shortcode( $this->the_order->get_total_refunded_for_item( $item_id ), $atts ) : '' );

				case 'item_total_tax_excl':
					return $this->aiow_price_shortcode( $the_order->get_item_total( $item, false, true ), $atts );

				case 'item_total_tax_incl':
					return $this->aiow_price_shortcode( $the_order->get_item_total( $item, true, true ), $atts );

				case 'item_subtotal_tax_excl':
					$return = ( is_callable( array( $item, 'get_subtotal' ) ) ? // could also use `is_object( $the_product )`
						$the_order->get_item_subtotal( $item, false, true ) :
						$the_order->get_item_total( $item, false, true )
					);
					return $this->aiow_price_shortcode( $return, $atts );

				case 'item_subtotal_tax_incl':
					$return = ( is_callable( array( $item, 'get_subtotal' ) ) ?
						$the_order->get_item_subtotal( $item, true, true ) :
						$the_order->get_item_total( $item, true, true )
					);
					return $this->aiow_price_shortcode( $return, $atts );

				case 'item_tax':
					return $this->aiow_price_shortcode( $the_order->get_item_tax( $item, true ), $atts );

				case 'line_total_tax_excl':
					$line_total_tax_excl = $the_order->get_line_total( $item, false, true );
					$line_total_tax_excl = apply_filters( 'aiow_line_total_tax_excl', $line_total_tax_excl, $the_order );
					return $this->aiow_price_shortcode( $line_total_tax_excl, $atts );

				case 'line_total_tax_incl':
					return $this->aiow_price_shortcode( $the_order->get_line_total( $item, true, true ), $atts );

				case 'line_subtotal_tax_excl':
					$return = ( is_callable( array( $item, 'get_subtotal' ) ) ?
						$the_order->get_line_subtotal( $item, false, true ) :
						$the_order->get_line_total( $item, false, true )
					);
					return $this->aiow_price_shortcode( $return, $atts );

				case 'line_subtotal_tax_incl':
					$return = ( is_callable( array( $item, 'get_subtotal' ) ) ?
						$the_order->get_line_subtotal( $item, true, true ) :
						$the_order->get_line_total( $item, true, true )
					);
					return $this->aiow_price_shortcode( $return, $atts );

				case 'line_tax':
					$line_tax = $the_order->get_line_tax( $item );
					$line_tax = apply_filters( 'aiow_line_tax', $line_tax, $the_order );
					return $this->aiow_price_shortcode( $line_tax, $atts );

				case 'line_subtax':
					$line_subtax = ( is_callable( array( $item, 'get_subtotal' ) ) ?
						$the_order->get_line_subtotal( $item, true, false ) - $the_order->get_line_subtotal( $item, false, false ) :
						$the_order->get_line_total( $item, true, false ) - $the_order->get_line_total( $item, false, false )
					);
					return $this->aiow_price_shortcode( $line_subtax, $atts );

				case 'item_tax_percent':
				case 'line_tax_percent':
					$item_total = $the_order->get_item_total( $item, false, false );
					$item_tax_percent = ( 0 != $item_total ) ? $the_order->get_item_tax( $item, false ) / $item_total * 100 : 0;
					$item_tax_percent = apply_filters( 'aiow_line_tax_percent', $item_tax_percent, $the_order );
					return sprintf( $atts['tax_percent_format'], $item_tax_percent );

				case 'item_weight':
				case 'product_weight':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_weight();

				case 'item_weight_multiply_qty':
				case 'product_weight_multiply_qty':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_weight() * $item['qty'];

				case 'item_width':
				case 'product_width':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_width();

				case 'item_height':
				case 'product_height':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_height();

				case 'item_length':
				case 'product_length':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_length();

				case 'product_cost':
					return ( ! is_object( $the_product ) ) ? '' :
						wc_price( $atts['multiply_cost'] * wc_get_product_purchase_price( $the_product->get_id() ) );

				case 'product_profit':
					return ( ! is_object( $the_product ) ) ? '' :
						wc_price( $atts['multiply_profit'] * ( $the_product->get_price() - wc_get_product_purchase_price( $the_product->get_id() ) ) );

				case 'line_cost':
					return ( ! is_object( $the_product ) ) ? '' :
						wc_price( $atts['multiply_cost'] * $item['qty'] * wc_get_product_purchase_price( $the_product->get_id() ) );

				case 'line_profit':
					return ( ! is_object( $the_product ) ) ? '' :
						wc_price( $atts['multiply_profit'] * $item['qty'] * ( $the_product->get_price() - wc_get_product_purchase_price( $the_product->get_id() ) ) );

				case 'product_id':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_id();

				case 'product_shipping_class':
					return ( ! is_object( $the_product ) ) ? '' :
						( '' != ( $shipping_class = $the_product->get_shipping_class() ) || ! isset( $column_param ) || '' == $column_param ? $shipping_class : $column_param );

				case 'product_shipping_class_id':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_shipping_class_id();

				case 'item_product_id':
					return ( true === $item['is_custom'] ) ? '' : $item['product_id'];

				case 'product_meta':
					return ( ! is_object( $the_product ) || ! isset( $column_param ) || '' == $column_param ) ? '' : $the_product->get_meta( $column_param );

				case 'product_post_meta':
					return ( true === $item['is_custom'] || ! isset( $column_param ) || '' == $column_param ) ? '' : get_post_meta( $item['product_id'], $column_param, true );

				case 'product_purchase_note':
					return ( ! is_object( $the_product ) ) ? '' : $the_product->get_purchase_note();

				case 'product_barcode':
					if ( is_object( $the_product ) ) {
						if ( ! isset( $column_param ) || '' == $column_param ) {
							$code = $the_product->get_sku();
						} else {
							switch ( $column_param ) {
								case '%id%':
									$code = $the_product->get_id();
									break;
								case '%sku%':
									$code = $the_product->get_sku();
									break;
								case '%url%':
									$code = $the_product->get_permalink();
									break;
								default: // meta
									$code = get_post_meta( $the_product->get_id(), $column_param, true );
							}
						}
						$atts = array(
							'code'                  => $code,
							'type'                  => 'PDF417',
							'dimension'             => '2D',
							'width'                 => $atts['product_barcode_width'],
							'height'                => $atts['product_barcode_height'],
							'color'                 => 'black',
						);
						return aiow_tcpdf_barcode( $atts );
					} else {
						return '';
					}

				default:
					return '';
			}
		}
	}
}
