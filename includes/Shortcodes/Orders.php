<?php
/**
 * Register Orders Shortcode
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Shortcodes;

if ( ! class_exists( 'Orders' ) ) {

	/**
	 * Declare `Orders` class extends `Shortcodes`
	 */
	class Orders extends Shortcodes {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->the_shortcodes = array(
				'aiow_order_billing_address',
				'aiow_order_billing_country_name',
				'aiow_order_billing_phone',
				'aiow_order_billing_email',
				'aiow_order_checkout_field',
				'aiow_order_coupons',
				'aiow_order_currency',
				'aiow_order_custom_field',
				'aiow_order_custom_meta_field',
				'aiow_order_customer_data',
				'aiow_order_customer_meta',
				'aiow_order_customer_note',
				'aiow_order_customer_user',
				'aiow_order_customer_user_roles',
				'aiow_order_date',
				'aiow_order_fee',
				'aiow_order_fees_html',
				'aiow_order_function',
				'aiow_order_id',
				'aiow_order_items',
				'aiow_order_items_cost',
				'aiow_order_items_meta',
				'aiow_order_items_total_number',
				'aiow_order_items_total_quantity',
				'aiow_order_items_total_weight',
				'aiow_order_meta',
				'aiow_order_notes',
				'aiow_order_number',
				'aiow_order_payment_method',
				'aiow_order_payment_method_transaction_id',
				'aiow_order_products_meta',
				'aiow_order_products_terms',
				'aiow_order_profit',
				'aiow_order_refunds_table',
				'aiow_order_remaining_refund_amount',
				'aiow_order_shipping_address',
				'aiow_order_shipping_country_name',
				'aiow_order_shipping_method',
				'aiow_order_shipping_price',
				'aiow_order_shipping_tax',
				'aiow_order_status',
				'aiow_order_status_label',
				'aiow_order_subtotal',
				'aiow_order_subtotal_by_tax_class',
				'aiow_order_subtotal_plus_shipping',
				'aiow_order_subtotal_to_display',
				'aiow_order_tax_by_class',
				'aiow_order_taxes_html',
				'aiow_order_tcpdf_barcode',
				'aiow_order_time',
				'aiow_order_total',
				'aiow_order_total_after_refund',
				'aiow_order_total_by_tax_class',
				'aiow_order_total_discount',
				'aiow_order_total_excl_shipping',
				'aiow_order_total_excl_tax',
				'aiow_order_total_fees',
				'aiow_order_total_fees_incl_tax',
				'aiow_order_total_fees_tax',
				'aiow_order_total_formatted',
				'aiow_order_total_height',
				'aiow_order_total_in_words',
				'aiow_order_total_length',
				'aiow_order_total_shipping_refunded',
				'aiow_order_total_refunded',
				'aiow_order_total_tax',
				'aiow_order_total_tax_after_refund',
				'aiow_order_total_tax_percent',
				'aiow_order_total_tax_refunded',
				'aiow_order_total_weight',
				'aiow_order_total_width',
			);

			parent::__construct();
		}

		/**
		 * Extra attribute.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function add_extra_atts( $atts ) {
			$modified_atts = array_merge( array(
				'order_id'                    => 0,
				'hide_currency'               => 'no',
				'excl_tax'                    => 'no',
				'date_format'                 => aiow_option( 'date_format' ),
				'time_format'                 => aiow_option( 'time_format' ),
				'hide_if_zero'                => 'no',
				'add_html_on_price'           => true,
				'field_id'                    => '',
				'name'                        => '',
				'round_by_line'               => 'no',
				'whole'                       => '',
				'decimal'                     => '&cent;',
				'precision'                   => aiow_option( 'woocommerce_price_num_decimals', 2 ),
				'lang'                        => 'EN',
				'unique_only'                 => 'no',
				'function_name'               => '',
				'sep'                         => ', ',
				'item_number'                 => 'all',
				'field'                       => 'name',
				'order_user_roles'            => '',
				'meta_key'                    => '',
				'tax_class'                   => '',
				'fallback_billing_address'    => 'no',
				'tax_display'                 => '',
				'table_class'                 => '',
				'columns_styles'              => '',
				'columns_titles'              => '',
				'columns'                     => '',
				'price_prefix'                => '',
				'display_refunded'            => 'yes',
				'insert_page_break'           => '',
				'key'                         => null,
				'days'                        => 0,
				'code'                        => '',
				'type'                        => '',
				'dimension'                   => '2D',
				'width'                       => 0,
				'height'                      => 0,
				'color'                       => 'black',
				'currency'                    => '',
				'doc_type'                    => 'invoice',
				'show_label'                  => true
			), $atts );

			return $modified_atts;
		}

		/**
		 * Init attributes.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function init_atts( $atts ) {
			$atts['excl_tax'] = ( 'yes' === $atts['excl_tax'] );

			if ( 0 == $atts['order_id'] ) {
				$atts['order_id'] = ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID();
			}
			if ( 0 == $atts['order_id'] ) {
				$atts['order_id'] = ( isset( $_GET['pdf_invoice'] ) ) ? $_GET['pdf_invoice'] : 0; // PDF Invoices V1 compatibility
			}
			if ( 0 == $atts['order_id'] ) {
				return false;
			}

			// Class properties
			$this->the_order = ( 'shop_order' === get_post_type( $atts['order_id'] ) ) ? wc_get_order( $atts['order_id'] ) : null;
			if ( ! $this->the_order ) {
				return false;
			}

			return $atts;
		}

		/**
		 * Extra check.
		 *
		 * @param array $atts Attributes.
		 * @return bool
		 */
		function extra_check( $atts ) {
			if ( '' != $atts['order_user_roles'] ) {
				$user_info = get_userdata( ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_user : $this->the_order->get_customer_id() ) );
				$user_roles = $user_info->roles;
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
		 * @param array  $atts Attribute.
		 * @return string
		 */
		private function aiow_price_shortcode( $raw_price, $atts ) {
			if ( 'yes' === $atts['hide_if_zero'] && 0 == $raw_price ) {
				return '';
			} else {
				$order_currency = aiow_get_order_currency( $this->the_order );
				if ( '' === $atts['currency'] ) {
					return aiow_price( $raw_price, $order_currency, $atts['hide_currency'], $atts );
				} else {
					$convert_to_currency = $atts['currency'];
					if ( '%shop_currency%' === $convert_to_currency ) {
						$convert_to_currency = aiow_option( 'woocommerce_currency' );
					}
					return aiow_price( $raw_price * aiow_get_saved_exchange_rate( $order_currency, $convert_to_currency ), $convert_to_currency, $atts['hide_currency'], $atts );
				}
			}
		}

		/**
		 * Order items.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_items_cost( $atts ) {
			$atts['type'] = 'items_cost';
			return $this->aiow_order_profit( $atts );
		}

		/**
		 * Order profit.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_profit( $atts ) {
			$total = 0;
			foreach ( $this->the_order->get_items() as $item_id => $item ) {
				$product_id = ( ( isset( $item['variation_id'] ) && 0 != $item['variation_id'] && 'no' === aiow_option( 'aiow_purchase_data_variable_as_simple_enabled', 'no' ) )
					? $item['variation_id'] : $item['product_id'] );
				$value = 0;
				if ( 0 != ( $purchase_price = wc_get_product_purchase_price( $product_id ) ) ) {
					if ( 'profit' === $atts['type'] || '' === $atts['type'] ) {
						// profit
						$_order_prices_include_tax = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->prices_include_tax : $this->the_order->get_prices_include_tax() );
						$line_total                = ( $_order_prices_include_tax ? ( $item['line_total'] + $item['line_tax'] ) : $item['line_total'] );
						$value                     = $line_total - $purchase_price * $item['qty'];
					} else {
						// 'items_cost'
						$value                     = $purchase_price * $item['qty'];
					}
				}
				$total += $value;
			}
			return $this->aiow_price_shortcode( $total, $atts );
		}

		/**
		 * Order tcpdf barcode.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_tcpdf_barcode( $atts ) {
			switch ( $atts['code'] ) {
				case '%url%':
					$atts['code'] = $this->the_order->get_view_order_url();
					break;
				case '%id%':
					$atts['code'] = $atts['order_id'];
					break;
				case '%doc_number%':
					$atts['code'] = aiow_get_invoice_number( $atts['order_id'], $atts['doc_type'] );
					break;
				case '%meta%':
					$atts['code'] = get_post_meta( $atts['order_id'], $atts['meta_key'], true );
					break;
				default:
					return '';
			}
			return aiow_tcpdf_barcode( $atts );
		}

		/**
		 * Order total format.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_total_formatted( $atts ) {
			return $this->the_order->get_formatted_order_total( $atts['tax_display'], ( 'yes' === $atts['display_refunded'] ) );
		}

		/**
		 * Order remaining & refund amount.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_remaining_refund_amount( $atts ) {
			return $this->aiow_price_shortcode( $this->the_order->get_remaining_refund_amount(), $atts );
		}

		/**
		 * Order refund table.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_refunds_table( $atts ) {
			$columns    = ( '' == $atts['columns'] ? array() : explode( '|', $atts['columns'] ) );
			$table_data = array();
			$i          = 1;
			foreach ( $this->the_order->get_refunds() as $_refund ) {
				$row = array();
				foreach ( $columns as $column ) {
					$cell = '';
					switch ( $column ) {
						case 'refund_number':
							$cell = $i;
							break;
						case 'refund_title':
							$cell = sprintf(
							/* translators: 1: refund id 2: refund date */
								esc_html__( 'Refund #%1$s - %2$s', 'woocommerce' ),
								esc_html( $_refund->get_id() ),
								esc_html( wc_format_datetime( $_refund->get_date_created(), aiow_option( 'date_format' ) . ', ' . aiow_option( 'time_format' ) ) )
							);
							break;
						case 'refund_date':
							$cell = esc_html( wc_format_datetime( $_refund->get_date_created(), aiow_option( 'date_format' ) . ', ' . aiow_option( 'time_format' ) ) );
							break;
						case 'refund_reason':
							$cell = $_refund->get_reason();
							break;
						case 'refund_reason_or_title':
							$reason = $_refund->get_reason();
							$cell = ( '' != $reason ? $reason : $_refund->get_post_title() );
							break;
						case 'refund_amount':
							$cell = $atts['price_prefix'] . $_refund->get_formatted_refund_amount();
							break;
						case 'refund_items':
							$_items = array();
							foreach ( $_refund->get_items() as $_item ) {
								$_items[] = $_item->get_name() . ' x ' . $_item->get_quantity() * -1;
							}
							$cell = ( ! empty( $_items ) ? implode( '<br>', $_items ) : '' );
							break;
					}
					$row[] = $cell;
				}
				$i++;
				$table_data[] = $row;
			}
			if ( empty( $table_data ) ) {
				return '';
			}
			$table_html_args = array(
				'table_class'        => $atts['table_class'],
				'columns_classes'    => array(),
				'columns_styles'     => ( '' == $atts['columns_styles'] ? array() : explode( '|', $atts['columns_styles'] ) ),
			);
			$columns_titles = array( ( '' == $atts['columns_titles'] ? array() : explode( '|', $atts['columns_titles'] ) ) );
			if ( '' != $atts['insert_page_break'] ) {
				$page_breaks  = explode ( '|', $atts['insert_page_break'] );
				$data_size    = count( $table_data );
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
					$data_slice    = array_slice( $table_data, $slice_offset, $current_page_break );
					$html         .= aiow_get_table_html( array_merge( $columns_titles, $data_slice ), $table_html_args );
					$slice_offset += $current_page_break;
					$slices++;
				}
			} else {
				$html = aiow_get_table_html( array_merge( $columns_titles, $table_data ), $table_html_args );
			}
			return $html;
		}

		/**
		 * Order refund total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_total_tax_refunded( $atts ) {
			return $this->aiow_price_shortcode( $this->the_order->get_total_tax_refunded(), $atts );
		}

		/**
		 * Order shipping refund total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_total_shipping_refunded( $atts ) {
			return $this->aiow_price_shortcode( $this->the_order->get_total_shipping_refunded(), $atts );
		}

		/**
		 * Order refunded total.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_total_refunded( $atts ) {
			return $this->aiow_price_shortcode( $this->the_order->get_total_refunded(), $atts );
		}

		/**
		 * Order customer meta.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_order_customer_meta( $atts ) {
			if ( '' != $atts['key'] && ( $_customer_id = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_user : $this->the_order->get_customer_id() ) ) ) {
				if ( '' != ( $meta = get_user_meta( $_customer_id, $atts['key'], true ) ) ) {
					return $meta;
				}
			}
			return '';
		}

		/**
		 * Order customer data.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_customer_data( $atts ) {
			if ( '' != $atts['key'] && ( $_customer_id = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_user : $this->the_order->get_customer_id() ) ) ) {
				if ( ( $user_data = get_userdata( $_customer_id ) ) && isset( $user_data->{$atts['key']} ) ) {
					return $user_data->{$atts['key']};
				}
			}
			return '';
		}

		/**
		 * Order customer role.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_customer_user_roles( $atts ) {
			$user_info = get_userdata( ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_user : $this->the_order->get_customer_id() ) );
			return implode( ', ', $user_info->roles );
		}

		/**
		 * Order customer user.
		 *
		 * @param array $atts Attributes.
		 * @return mixed
		 */
		function aiow_order_customer_user( $atts ) {
			return ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_user : $this->the_order->get_customer_id() );
		}

		/**
		 * Order coupons.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_coupons( $atts ) {
			return implode( ', ', $this->the_order->get_used_coupons() );
		}

		/**
		 * Order functiom.
		 *
		 * @param array $atts Attributes.
		 * @return mixed
		 */
		function aiow_order_function( $atts ) {
			$function_name = $atts['function_name'];
			if ( '' != $function_name && method_exists( $this->the_order, $function_name ) ) {
				$return = $this->the_order->$function_name();
				return ( is_array( $return ) ) ? implode( ', ', $return ) : $return;
			}
		}

		/**
		 * Order custom field.
		 *
		 * @param array $atts Attributes.
		 * @return mixed
		 */
		function aiow_order_custom_field( $atts ) {
			$order_custom_fields = get_post_custom( $atts['order_id'] );
			$return = ( isset( $order_custom_fields[ $atts['name'] ][0] ) ) ? $order_custom_fields[ $atts['name'] ][0] : '';
			if ( null !== $atts['key'] ) {
				$return = maybe_unserialize( $return );
				return ( isset( $return[ $atts['key'] ] ) ? $return[ $atts['key'] ] : '' );
			}
			return $return;
		}

		/**
		 * Order total fees.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_fees( $atts ) {
			$total_fees = 0;
			$the_fees = $this->the_order->get_fees();
			foreach ( $the_fees as $the_fee ) {
				$total_fees += $the_fee['line_total'];
			}
			return $this->aiow_price_shortcode( $total_fees, $atts );
		}

		/**
		 * Order total fees tax.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_fees_tax( $atts ) {
			$total_fees_tax = 0;
			$the_fees = $this->the_order->get_fees();
			foreach ( $the_fees as $the_fee ) {
				$total_fees_tax += $this->the_order->get_line_tax( $the_fee );
			}
			return $this->aiow_price_shortcode( $total_fees_tax, $atts );
		}

		/**
		 * Order total including tax.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_fees_incl_tax( $atts ) {
			$total_fees = 0;
			$the_fees = $this->the_order->get_fees();
			foreach ( $the_fees as $the_fee ) {
				$total_fees += $the_fee['line_total'];
				$total_fees += $this->the_order->get_line_tax( $the_fee );
			}
			return $this->aiow_price_shortcode( $total_fees, $atts );
		}

		/**
		 * Order fees html.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_fees_html( $atts ) {
			$fees_html = '';
			$the_fees = $this->the_order->get_fees();
			foreach ( $the_fees as $the_fee ) {
				$fees_html .= '<p>' . $the_fee['name'] . ' - ' . $the_fee['line_total'] . '</p>';
			}
			return $fees_html;
		}

		/**
		 * Order fees.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_fee( $atts ) {
			if ( '' == $atts['name'] ) return '';
			$the_fees = $this->the_order->get_fees();
			foreach ( $the_fees as $the_fee ) {
				if ( $atts['name'] == $the_fee['name'] ) {
					return $this->aiow_price_shortcode( $the_fee['line_total'], $atts );
				}
			}
			return '';
		}
		/**
		 * Order shipping method.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_shipping_method( $atts ) {
			return $this->the_order->get_shipping_method();
		}

		/**
		 * Order transaction ID.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_payment_method_transaction_id( $atts ) {
			return $this->the_order->get_transaction_id();
		}

		/**
		 * Order payment method.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_payment_method( $atts ) {
			return get_post_meta( aiow_get_order_id( $this->the_order ), '_payment_method_title', true );
		}

		/**
		 * Order total width.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_width( $atts ) {
			return $this->get_order_total( $atts, 'width' );
		}

		/**
		 * Order total height.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_height( $atts ) {
			return $this->get_order_total( $atts, 'height' );
		}

		/**
		 * Order total length.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_length( $atts ) {
			return $this->get_order_total( $atts, 'length' );
		}

		/**
		 * Order total weight.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_weight( $atts ) {
			return $this->get_order_total( $atts, 'weight' );
		}

		/**
		 * Get order total.
		 *
		 * @param array $atts Attributes.
		 * @param array $param Parameters.
		 * @return string
		 */
		function get_order_total( $atts, $param ) {
			$total = 0;
			$the_items = $this->the_order->get_items();
			foreach ( $the_items as $item_id => $item ) {
				$product_id = ( 0 != $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				$_product = wc_get_product( $product_id );
				if ( $_product ) {
					switch ( $param ) {
						case 'width':
							$total += ( $item['qty'] * floatval( $_product->get_width() ) );
							break;
						case 'height':
							$total += ( $item['qty'] * floatval( $_product->get_height() ) );
							break;
						case 'length':
							$total += ( $item['qty'] * floatval( $_product->get_length() ) );
							break;
						case 'weight':
							$total += ( $item['qty'] * floatval( $_product->get_weight() ) );
							break;
					}
				}
			}
			return ( 0 == $total && 'yes' === $atts['hide_if_zero'] ) ? '' : $total;
		}

		/**
		 * Order items total weight.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_items_total_weight( $atts ) {
			return $this->get_order_total( $atts, 'weight' );
		}

		/**
		 * Order total quantity.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_items_total_quantity( $atts ) {
			$total_quantity = 0;
			$the_items = $this->the_order->get_items();
			foreach( $the_items as $the_item ) {
				$total_quantity += $the_item['qty'];
			}
			return ( 0 == $total_quantity && 'yes' === $atts['hide_if_zero'] ) ? '' : $total_quantity;
		}

		/**
		 * Order total items number.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_items_total_number( $atts ) {
			$total_number = count( $this->the_order->get_items() );
			return ( 0 == $total_number && 'yes' === $atts['hide_if_zero'] ) ? '' : $total_number;
		}

		/**
		 * Order billing address.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_billing_address( $atts ) {
			return apply_filters( 'aiow_order_billing_address', $this->the_order->get_formatted_billing_address(), $atts );
		}

		/**
		 * Order billing email.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_billing_email( $atts ) {
			return apply_filters( 'aiow_order_billing_email', $this->the_order->get_billing_email(), $atts );
		}

		/**
		 * Get order notes.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_notes( $atts ) {
			$notes = array();
			if ( '' == $atts['type'] || 'customer_notes' === $atts['type'] ) {
				foreach ( $this->the_order->get_customer_order_notes() as $note ) {
					$notes[] = $note->comment_content;
				}
			} else { // 'private_notes' or 'all_notes'
				$args  = array(
					'post_id' => aiow_get_order_id( $this->the_order ),
					'approve' => 'approve',
					'type'    => '',
				);
				remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );
				$comments = get_comments( $args );
				foreach ( $comments as $comment ) {
					if ( 'private_notes' === $atts['type'] && get_comment_meta( $comment->comment_ID, 'is_customer_note', true ) ) {
						continue;
					}
					$notes[] = make_clickable( $comment->comment_content );
				}
				add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );
			}
			if ( isset( $atts['limit'] ) && $atts['limit'] > 0 ) {
				$notes = array_slice( $notes, 0, $atts['limit'] );
			}
			return implode( $atts['sep'], $notes );
		}

		/**
		 * Order customer note.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_customer_note( $atts ) {
			return ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->customer_note : $this->the_order->get_customer_note() );
		}

		/**
		 * Order billing country name.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_billing_country_name( $atts ) {
			$country_code = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->billing_country : $this->the_order->get_billing_country() );
			if ( false !== ( $country_name = aiow_get_country_name_by_code( $country_code ) ) ) {
				return $country_name;
			} else {
				return $country_code;
			}
		}

		/**
		 * Order shipping country name.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_shipping_country_name( $atts ) {
			$country_code = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->shipping_country : $this->the_order->get_shipping_country() );
			if ( false !== ( $country_name = aiow_get_country_name_by_code( $country_code ) ) ) {
				return $country_name;
			} else {
				return $country_code;
			}
		}

		/**
		 * Order billing phone.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_billing_phone( $atts ) {
			return ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->billing_phone : $this->the_order->get_billing_phone() );
		}

		/**
		 * Get order items.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_items( $atts ) {
			$items = array();
			$the_items = $this->the_order->get_items();
			foreach ( $the_items as $item_id => $item ) {
				switch ( $atts['field'] ) {
					case '_debug':
						$items[] = '<pre>' . print_r( $item, true ) . '</pre>';
						break;
					case '_qty_x_name':
						$items[] = ( isset( $item['qty'] ) && isset( $item['name'] ) ) ? $item['qty'] . ' x ' . $item['name'] : '';
						break;
					case '_sku':
						$_product_id = ( 0 != $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
						if ( $_product = wc_get_product( $_product_id ) ) {
							$items[] = $_product->get_sku();
						}
						break;
					default:
						$items[] = ( isset( $item[ $atts['field'] ] ) ) ? $item[ $atts['field'] ] : '';
						break;
				}
			}
			if ( empty( $items ) ) {
				return '';
			}
			if ( 'all' === $atts['item_number'] ) {
				return implode( $atts['sep'], $items );
			} else {
				switch ( $atts['item_number'] ) {
					case 'first':
						return current( $items );
					case 'last':
						return end( $items );
					default:
						$item_number = intval( $atts['item_number'] ) - 1;
						if ( $item_number < 0 ) {
							$item_number = 0;
						} elseif ( $item_number >= count( $items ) ) {
							$item_number = count( $items ) - 1;
						}
						return $items[ $item_number ];
				}
			}
		}

		/**
		 * Get product terms.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_products_terms( $atts ) {
			if ( '' === $atts['taxonomy'] ) {
				return '';
			}
			$terms = array();
			$items = $this->the_order->get_items();
			foreach ( $items as $item_id => $item ) {
				$product_terms = get_the_terms( $item['product_id'], $atts['taxonomy'] );
				if ( ! empty( $product_terms ) && ! is_wp_error( $product_terms ) ) {
					foreach( $product_terms as $product_term ) {
						$terms[] = $product_term->name;
					}
				}
			}
			if ( 'yes' === $atts['unique_only'] ) {
				$terms = array_unique( $terms );
			}
			sort( $terms );
			return ( ! empty( $terms ) ? implode( $atts['sep'], $terms ) : '' );
		}

		/**
		 * Get product meta.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_products_meta( $atts ) {
			if ( '' === $atts['meta_key'] ) {
				return '';
			}
			$metas = array();
			$items = $this->the_order->get_items();
			foreach ( $items as $item_id => $item ) {
				$product_id = ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );
				if ( '' != ( $meta = get_post_meta( $product_id, $atts['meta_key'], true ) ) ) {
					$metas[] = $meta;
				}
			}
			if ( 'yes' === $atts['unique_only'] ) {
				$metas = array_unique( $metas );
			}
			return ( ! empty( $metas ) ? implode( $atts['sep'], $metas ) : '' );
		}

		/**
		 * Get order item meta.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_items_meta( $atts ) {
			if ( '' === $atts['meta_key'] ) {
				return '';
			}
			$items_metas = array();
			$the_items = $this->the_order->get_items();
			foreach ( $the_items as $item_id => $item ) {
				$the_meta = ( AIOW_IS_WC_VERSION_BELOW_3 ? $this->the_order->get_item_meta( $item_id, $atts['meta_key'], true ) : wc_get_order_item_meta( $item_id, $atts['meta_key'], true ) );
				if ( '' != $the_meta ) {
					$items_metas[] = $the_meta;
				}
			}
			if ( 'yes' === $atts['unique_only'] ) {
				$items_metas = array_unique( $items_metas );
			}
			return ( ! empty( $items_metas ) ? implode( $atts['sep'], $items_metas ) : '' );
		}

		/**
		 * Get order meta.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_meta( $atts ) {
			return ( '' != $atts['meta_key'] ? get_post_meta( aiow_get_order_id( $this->the_order ), $atts['meta_key'], true ) : '' );
		}

		/**
		 * Get order custom field.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_custom_meta_field( $atts ) {
			return $this->aiow_order_checkout_field( $atts );
		}

		/**
		 * Get order checkout field.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_checkout_field( $atts ) {
			$field_id = ( string ) $atts['field_id'];
			if ( '' == $field_id ) {
				return '';
			}
			if ( AIOW_IS_WC_VERSION_BELOW_3 ) {
				if ( ! isset( $this->the_order->$field_id ) ) {
					return '';
				}
				$field_value = $this->the_order->$field_id;
				return ( is_array( $field_value ) && isset( $field_value['value'] ) ) ? $field_value['value'] : $field_value;
			} else {
				$order_data = $this->the_order->get_data();
				if ( substr( $field_id, 0, 8 ) === 'billing_' ) {
					$billing_field_id = substr( $field_id, 8 );
					if ( isset( $order_data['billing'][ $billing_field_id ] ) ) {
						return $order_data['billing'][ $billing_field_id ];
					}
				} elseif ( substr( $field_id, 0, 9 ) === 'shipping_' ) {
					$shipping_field_id = substr( $field_id, 9 );
					if ( isset( $order_data['shipping'][ $shipping_field_id ] ) ) {
						return $order_data['shipping'][ $shipping_field_id ];
					}
				}
				if ( $this->the_order->get_meta( '_' . $field_id ) ) {
					return $this->the_order->get_meta( '_' . $field_id );
				}
				if ( isset( $order_data[ $field_id ] ) ) {
					return ( is_array( $order_data[ $field_id ] ) && isset( $order_data[ $field_id ]['value'] ) ) ? $order_data[ $field_id ]['value'] : $order_data[ $field_id ];
				} else {
					return '';
				}
			}
		}

		/**
		 * Get order shipping address.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_shipping_address( $atts ) {
			$shipping_address = $this->the_order->get_formatted_shipping_address();
			if ( '' != $shipping_address ) {
				return $shipping_address;
			} elseif ( 'yes' === $atts['fallback_billing_address'] ) {
				return $this->the_order->get_formatted_billing_address();
			} else {
				return '';
			}
		}

		/**
		 * Get order status.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_status( $atts ) {
			return $this->the_order->get_status();
		}

		/**
		 * Get order label.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_status_label( $atts ) {
			$status_object = get_post_status_object( 'wc-' . $this->the_order->get_status() );
			return ( isset( $status_object->label ) ) ? $status_object->label : '';
		}

		/**
		 * Get order date.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_date( $atts ) {
			return date_i18n( $atts['date_format'], strtotime( aiow_get_order_date( $this->the_order ) ) + $atts['days'] * 24 * 60 * 60 );
		}

		/**
		 * Get order time.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_time( $atts ) {
			$order_date = aiow_get_order_date( $this->the_order )->format( 'Y-m-d H:i:s' );
			return aiow_pretty_utc_date( $order_date, $atts['time_format'] );
		}

		/**
		 * Get order number.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_number( $atts ) {
			return $this->the_order->get_order_number();
		}

		/**
		 * Get order ID.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_id( $atts ) {
			return $atts['order_id'];
		}

		/**
		 * Get order shipping price.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_shipping_price( $atts ) {
			$the_result = ( $atts['excl_tax'] ) ? $this->the_order->get_total_shipping() : $this->the_order->get_total_shipping() + $this->the_order->get_shipping_tax();
			return $this->aiow_price_shortcode( $the_result, $atts );
		}

		/**
		 * Get order total discount.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_discount( $atts ) {
			$the_discount = $this->the_order->get_total_discount( $atts['excl_tax'] );
			return $this->aiow_price_shortcode( $the_discount, $atts );
		}

		/**
		 * Get order shipping tax.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_shipping_tax( $atts ) {
			return $this->aiow_price_shortcode( $this->the_order->get_shipping_tax(), $atts );
		}

		/**
		 * Get order total tax percen.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_tax_percent( $atts ) {
			$order_total_tax_not_rounded = $this->the_order->get_cart_tax() + $this->the_order->get_shipping_tax();
			$order_total_excl_tax        = $this->the_order->get_total() - $order_total_tax_not_rounded;
			$order_total_tax_percent = ( 0 == $order_total_excl_tax ) ? 0 : $order_total_tax_not_rounded / $order_total_excl_tax * 100;
			$order_total_tax_percent = round( $order_total_tax_percent, $atts['precision'] );
			apply_filters( 'aiow_order_total_tax_percent', $order_total_tax_percent, $this->the_order );
			return number_format( $order_total_tax_percent, $atts['precision'] );
		}

		/**
		 * Get order sub total tax class.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_subtotal_by_tax_class( $atts ) {
			$subtotal_by_tax_class = 0;
			$tax_class = ( 'standard' === $atts['tax_class'] ) ? '' : $atts['tax_class'];
			foreach ( $this->the_order->get_items() as $item ) {
				if ( $tax_class === $item['tax_class'] ) {
					$subtotal_by_tax_class += $item['line_subtotal'];
				}
			}
			return $this->aiow_price_shortcode( $subtotal_by_tax_class, $atts );
		}

		/**
		 * Get order total by tax class.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_by_tax_class( $atts ) {
			$total_by_tax_class = 0;
			$tax_class = ( 'standard' === $atts['tax_class'] ) ? '' : $atts['tax_class'];
			foreach ( $this->the_order->get_items() as $item ) {
				if ( $tax_class === $item['tax_class'] ) {
					$total_by_tax_class += $item['line_total'];
				}
			}
			return $this->aiow_price_shortcode( $total_by_tax_class, $atts );
		}

		/**
		 * Get order tax class.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_tax_by_class( $atts ) {
			$tax_class = ( 'standard' === $atts['tax_class'] ) ? '' : $atts['tax_class'];
			$total_tax_by_class = 0;
			foreach ( $this->the_order->get_items() as $item ) {
				if ( $tax_class === $item['tax_class'] ) {
					$total_tax_by_class += $item['line_tax'];
				}
			}
			return $this->aiow_price_shortcode( $total_tax_by_class, $atts );
		}

		/**
		 * Get order tax html.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_taxes_html( $atts ) {
			$order_taxes = $this->the_order->get_taxes();
			$taxes_html  = '';
			foreach ( $order_taxes as $order_tax ) {
				if ( true === filter_var( $atts['show_label'], FILTER_VALIDATE_BOOLEAN ) ) {
					$taxes_html .= ( isset( $order_tax['label'] ) ) ? $order_tax['label'] . ': ' : '';
				}
				$amount     = 0;
				$amount     += ( isset( $order_tax['tax_amount'] ) ) ? $order_tax['tax_amount'] : 0;
				$amount     += ( isset( $order_tax['shipping_tax_amount'] ) ) ? $order_tax['shipping_tax_amount'] : 0;
				$taxes_html .= $this->aiow_price_shortcode( $amount, $atts ) . '<br>';
			}
			return $taxes_html;
		}

		/**
		 * Get order total tax.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_tax( $atts ) {
			return $this->aiow_price_shortcode( apply_filters( 'aiow_order_total_tax', $this->the_order->get_total_tax(), $this->the_order ), $atts );
		}

		/**
		 * Get order total tax after refund.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_tax_after_refund( $atts ) {
			return $this->aiow_price_shortcode( ( $this->the_order->get_total_tax() - $this->the_order->get_total_tax_refunded() ), $atts );
		}

		/**
		 * Get order subtotal plus shipping.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_subtotal_plus_shipping( $atts ) {
			$the_subtotal = $this->the_order->get_subtotal();
			$the_shipping = $this->the_order->get_total_shipping();
			$fees_total   = 0;
			if ( isset( $atts['plus_fees'] ) && true === filter_var( $atts['plus_fees'], FILTER_VALIDATE_BOOLEAN ) ) {
				$fees_total = aiow_get_order_fees_total( $this->the_order );
			}
			return $this->aiow_price_shortcode( $the_subtotal + $the_shipping + $fees_total, $atts );
		}

		/**
		 * Get order subtotal.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_subtotal( $atts ) {

			if ( 'yes' === $atts['round_by_line'] ) {
				$the_subtotal = 0;
				foreach ( $this->the_order->get_items() as $item ) {
					$the_subtotal += $this->the_order->get_line_subtotal( $item, false, true );
				}
			} else {
				$the_subtotal = $this->the_order->get_subtotal();
			}

			return $this->aiow_price_shortcode( $the_subtotal, $atts );
		}

		/**
		 * Get order subtotal display.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_subtotal_to_display( $atts ) {
			return $this->the_order->get_subtotal_to_display( false, $atts['tax_display'] );
		}

		/**
		 * Get order total excl tax.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_excl_tax( $atts ) {
			$order_total = $this->the_order->get_total() - $this->the_order->get_total_tax();
			$order_total = apply_filters( 'aiow_order_total_excl_tax', $order_total, $this->the_order );
			return $this->aiow_price_shortcode( $order_total, $atts );
		}

		/**
		 * Get order currency.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_currency( $atts ) {
			return aiow_get_order_currency( $this->the_order );
		}

		/**
		 * Get order total excl shipping.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_excl_shipping( $atts ) {
			$order_total_excl_shipping = ( true === $atts['excl_tax'] ) ?
				$this->the_order->get_total() - $this->the_order->get_total_shipping() - $this->the_order->get_total_tax() :
				$this->the_order->get_total() - $this->the_order->get_total_shipping() - $this->the_order->get_shipping_tax();
			return $this->aiow_price_shortcode( $order_total_excl_shipping, $atts );
		}

		/**
		 * Get order total.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total( $atts ) {
			$order_total = ( true === $atts['excl_tax'] ) ? $this->the_order->get_total() - $this->the_order->get_total_tax() : $this->the_order->get_total();
			return $this->aiow_price_shortcode( $order_total, $atts );
		}

		/**
		 * Get order total after refund.
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_after_refund( $atts ) {
			$order_total_after_refund = $this->the_order->get_total() - $this->the_order->get_total_refunded();
			if ( true === $atts['excl_tax'] ) {
				$order_total_after_refund -= ( $this->the_order->get_total_tax() - $this->the_order->get_total_tax_refunded() );
			}
			return $this->aiow_price_shortcode( $order_total_after_refund, $atts );
		}

		/**
		 * MB uc first.
		 *
		 * @param array $string string.
		 * @return string
		 */
		function mb_ucfirst( $string ) {
			return mb_strtoupper( mb_substr( $string, 0, 1 ) ) . mb_substr( $string, 1 );
		}

		/**
		 * Get order total in words
		 *
		 * @param array $atts Attributes.
		 * @return string
		 */
		function aiow_order_total_in_words( $atts ) {
			$order_total          = ( true === $atts['excl_tax'] ) ? $this->the_order->get_total() - $this->the_order->get_total_tax() : $this->the_order->get_total();
			$order_total_whole    = intval( $order_total );
			$order_total_decimal  = round( ( $order_total - $order_total_whole ) * 100 );

			$the_number_in_words  = '%s %s';
			$the_number_in_words .= ( 0 != $order_total_decimal ) ? ', %s %s.' : '.';

			$whole   = ( '' === $atts['whole'] ?
				( isset( $atts['use_currency_symbol'] ) && 'yes' === $atts['use_currency_symbol'] ?
					get_woocommerce_currency_symbol( $this->the_order->get_currency() ) : $this->the_order->get_currency()
				) : $atts['whole'] );
			$decimal = $atts['decimal'];

			switch ( $atts['lang'] ) {
				case 'LT':
				return sprintf( $the_number_in_words,
					$this->mb_ucfirst( convert_number_to_words_lt( $order_total_whole ) ),
					$whole,
					$this->mb_ucfirst( convert_number_to_words_lt( $order_total_decimal ) ),
					$decimal );
				case 'BG':
				return sprintf( $the_number_in_words,
					$this->mb_ucfirst( trim( convert_number_to_words_bg( $order_total_whole ) ) ),
					$whole,
					$this->mb_ucfirst( trim( convert_number_to_words_bg( $order_total_decimal ) ) ),
					$decimal );
				default: // 'EN'
				return sprintf( $the_number_in_words,
					ucfirst( convert_number_to_words( $order_total_whole ) ),
					$whole,
					ucfirst( convert_number_to_words( $order_total_decimal ) ),
					$decimal );
			}
		}
	}
}
