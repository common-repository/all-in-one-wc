<?php
/**
 * Shipping Module - Max Products per User.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Max_Products_Per_User' ) ) {

	/**
	 * Declare class `Max_Products_Per_User` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Max_Products_Per_User extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id         = 'max_products_per_user';
			$this->short_desc = __( 'Maximum Products per User', 'all-in-one-wc' );
			$this->desc       = __( 'Limit number of items your (logged) customers can buy (Free version allows to limit globally).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Limit number of items your (logged) customers can buy.', 'all-in-one-wc' );
			$this->extra_desc = __( 'Please note, that there is no maximum quantity set for not-logged (i.e. guest) users. Product quantities are updated, when order status is changed to status listed in module\'s "Order Status" option.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-maximum-products-per-user';
			parent::__construct();

			if ( $this->is_enabled() ) {
				if ( 'yes' === aiow_option( 'aiow_max_products_per_user_global_enabled', 'no' ) || 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_max_products_per_user_local_enabled', 'no' ) ) ) {
					add_action( 'woocommerce_checkout_process', array( $this, 'check_cart_quantities' ), PHP_INT_MAX );
					add_action( 'woocommerce_before_cart',      array( $this, 'check_cart_quantities' ), PHP_INT_MAX );
					if ( 'yes' === aiow_option( 'aiow_max_products_per_user_stop_from_adding_to_cart', 'no' ) ) {
						add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_on_add_to_cart' ), PHP_INT_MAX, 3 );
					}
					if ( 'yes' === aiow_option( 'aiow_max_products_per_user_stop_from_seeing_checkout', 'no' ) ) {
						add_action( 'wp', array( $this, 'stop_from_seeing_checkout' ), PHP_INT_MAX );
					}
					if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_max_products_per_user_local_enabled', 'no' ) ) ) {
						add_action( 'add_meta_boxes',    array( $this, 'add_meta_box' ) );
						add_action( 'save_post_product', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );
					}
				}
				$this->order_status = aiow_option( 'aiow_max_products_per_user_order_status', array( 'wc-completed' ) );
				if ( empty( $this->order_status ) ) {
					$this->order_status = array( 'wc-completed' );
				}
				foreach ( $this->order_status as $status ) {
					$status = substr( $status, 3 );
					add_action( 'woocommerce_order_status_' . $status, array( $this, 'save_quantities' ), PHP_INT_MAX );
				}
				add_action( 'add_meta_boxes', array( $this, 'add_report_meta_box' ) );
				add_action( 'admin_init', array( $this, 'calculate_data' ) );
				add_action( 'admin_notices',  array( $this, 'calculate_data_notice' ) );
			}
		}

		/**
		 * Add to cart validation.
		 *
		 * @param bool $passed Validation.
		 * @param int  $product_id Product ID.
		 * @param int  $quantity Qty.
		 * @return bool
		 */
		function validate_on_add_to_cart( $passed, $product_id, $quantity ) {
			// Get max quantity.
			if ( 0 == ( $max_qty = $this->get_max_qty( $product_id ) ) ) {
				return $passed;
			}
			// Get quantity already bought (for current user / current product).
			if ( 0 == ( $current_user_id = aiow_get_current_user_id() ) ) {
				return $passed;
			}
			$user_already_bought = 0;
			if ( ( $users_quantities = get_post_meta( $product_id, '_' . 'aiow_max_products_per_user_report', true ) ) && isset( $users_quantities[ $current_user_id ] ) ) {
				$user_already_bought = $users_quantities[ $current_user_id ];
			}
			// Get quantity in cart.
			$currently_in_cart = 0;
			if ( isset( WC()->cart ) ) {
				$cart_item_quantities = WC()->cart->get_cart_item_quantities();
				if ( ! empty( $cart_item_quantities ) && is_array( $cart_item_quantities ) ) {
					foreach ( $cart_item_quantities as $_product_id => $cart_item_quantity ) {
						if ( $_product_id == $product_id ) {
							$currently_in_cart += $cart_item_quantity;
						}
					}
				}
			}
			// Validate.
			if ( ( $currently_in_cart + $user_already_bought + $quantity ) > $max_qty ) {
				$product = wc_get_product( $product_id );
				$replaced_values = array(
					'%max_qty%'             => $max_qty,
					'%product_title%'       => $product->get_title(),
					'%qty_already_bought%'  => $user_already_bought,
					'%remaining_qty%'       => max( ( $max_qty - $user_already_bought ), 0 ),
				);
				$message = aiow_option( 'aiow_max_products_per_user_message',
					__( 'You can only buy maximum %max_qty% pcs. of %product_title% (you already bought %qty_already_bought% pcs.).', 'all-in-one-wc' ) );
				$message = str_replace( array_keys( $replaced_values ), $replaced_values, $message );
				wc_add_notice( $message, 'error' );
				return false;
			}
			// Passed.
			return $passed;
		}

		/**
		 * Calculate data notice.
		 */
		function calculate_data_notice() {
			if ( isset( $_GET['aiow_max_products_per_user_calculate_data_finished'] ) ) {
				$class = 'notice notice-info';
				$message = __( 'Data re-calculated.', 'all-in-one-wc' ) . ' ' .
					sprintf( __( '%s order(s) processed.', 'all-in-one-wc' ), $_GET['aiow_max_products_per_user_calculate_data_finished'] );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Calculate data.
		 */
		function calculate_data() {
			if ( isset( $_GET['aiow_max_products_per_user_calculate_data'] ) ) {
				$offset       = 0;
				$block_size   = 512;
				$total_orders = 0;
				while( true ) {
					$args = array(
						'post_type'      => 'shop_order',
						'post_status'    => $this->order_status,
						'posts_per_page' => $block_size,
						'orderby'        => 'ID',
						'order'          => 'DESC',
						'offset'         => $offset,
						'fields'         => 'ids',
					);
					$loop = new WP_Query( $args );
					if ( ! $loop->have_posts() ) {
						break;
					}
					foreach ( $loop->posts as $_order_id ) {
						$this->save_quantities( $_order_id );
						$total_orders++;
					}
					$offset += $block_size;
				}
				wp_safe_redirect( add_query_arg( 'aiow_max_products_per_user_calculate_data_finished', $total_orders,
					remove_query_arg( 'aiow_max_products_per_user_calculate_data' ) ) );
				exit;
			}
		}

		/**
		 * Register report meta box.
		 */
		function add_report_meta_box() {
			add_meta_box(
				'wc-jetpack-' . $this->id . '-report',
				 __( 'Maximum Products per User: Sales Data', 'all-in-one-wc' ),
				array( $this, 'create_report_meta_box' ),
				'product',
				'normal',
				'high'
			);
		}

		/**
		 * Display report meta box.
		 */
		function create_report_meta_box() {
			if ( $users_quantities = get_post_meta( get_the_ID(), '_' . 'aiow_max_products_per_user_report', true ) ) {
				$table_data = array();
				$table_data[] = array( __( 'User ID', 'all-in-one-wc' ), __( 'User Name', 'all-in-one-wc' ), __( 'Qty Bought', 'all-in-one-wc' ) );
				foreach ( $users_quantities as $user_id => $qty_bought ) {
					if ( 0 == $user_id ) {
						$user = __( 'Guest', 'all-in-one-wc' );
					} else {
						$user = get_user_by( 'id', $user_id );
						$user = ( isset( $user->data->user_nicename ) ? $user->data->user_nicename : '-' );
					}
					$table_data[] = array( $user_id, $user, $qty_bought );
				}
				echo aiow_get_table_html( $table_data, array( 'table_class' => 'widefat striped', 'table_heading_type' => 'horizontal' ) );
			} else {
				echo '<em>' . __( 'No data yet.', 'all-in-one-wc' ) . '</em>';
			}
		}

		/**
		 * Save QTY.
		 *
		 * @param int $order_id Order ID.
		 */
		function save_quantities( $order_id ) {
			if ( $order = wc_get_order( $order_id ) ) {
				if ( 'yes' !== get_post_meta( $order_id, '_' . 'aiow_max_products_per_user_saved', true ) ) {
					if ( sizeof( $order->get_items() ) > 0 ) {
						$user_id = ( aiow_IS_WC_VERSION_BELOW_3 ? $order->customer_user : $order->get_customer_id() );
						foreach ( $order->get_items() as $item ) {
							if ( $item->is_type( 'line_item' ) && ( $product = $item->get_product() ) ) {
								$product_id  = aiow_get_product_id_or_variation_parent_id( $product );
								$product_qty = $item->get_quantity();
								if ( '' == ( $users_quantities = get_post_meta( $product_id, '_' . 'aiow_max_products_per_user_report', true ) ) ) {
									$users_quantities = array();
								}
								if ( isset( $users_quantities[ $user_id ] ) ) {
									$product_qty += $users_quantities[ $user_id ];
								}
								$users_quantities[ $user_id ] = $product_qty;
								update_post_meta( $product_id, '_' . 'aiow_max_products_per_user_report', $users_quantities );
							}
						}
					}
					update_post_meta( $order_id, '_' . 'aiow_max_products_per_user_saved', 'yes' );
				}
			}
		}

		/**
		 * Get max QTY.
		 *
		 * @param int $product_id Product ID.
		 * @return bool
		 */
		function get_max_qty( $product_id ) {
			if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_max_products_per_user_local_enabled', 'no' ) ) && 0 != ( $qty = get_post_meta( $product_id, '_' . 'aiow_max_products_per_user_qty', true ) ) ) {
				return $qty;
			} elseif ( 'yes' === aiow_option( 'aiow_max_products_per_user_global_enabled', 'no' ) ) {
				return aiow_option( 'aiow_max_products_per_user_global_max_qty', 1 );
			} else {
				return 0;
			}
		}

		/**
		 * Stop from seeing checkout.
		 */
		function stop_from_seeing_checkout() {
			if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
				return;
			}
			if ( ! $this->check_quantities( false ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}

		/**
		 * Check cart quantities.
		 */
		function check_cart_quantities() {
			$this->check_quantities();
		}

		/**
		 * Check quantities.
		 *
		 * @param bool $add_notices Add notices.
		 * @return mixed
		 */
		function check_quantities( $add_notices = true ) {
			$result = true;
			if ( ! isset( WC()->cart ) ) {
				return $result;
			}
			if ( 0 == ( $current_user_id = aiow_get_current_user_id() ) ) {
				return $result;
			}
			$cart_item_quantities = WC()->cart->get_cart_item_quantities();
			if ( empty( $cart_item_quantities ) || ! is_array( $cart_item_quantities ) ) {
				return $result;
			}
			$is_cart = ( function_exists( 'is_cart' ) && is_cart() );
			foreach ( $cart_item_quantities as $_product_id => $cart_item_quantity ) {
				if ( 0 == ( $max_qty = $this->get_max_qty( $_product_id ) ) ) {
					continue;
				}
				$user_already_bought = 0;
				if ( ( $users_quantities = get_post_meta( $_product_id, '_' . 'aiow_max_products_per_user_report', true ) ) && isset( $users_quantities[ $current_user_id ] ) ) {
					$user_already_bought = $users_quantities[ $current_user_id ];
				}
				if ( ( $user_already_bought + $cart_item_quantity ) > $max_qty ) {
					if ( $add_notices ) {
						$result = false;
						$product = wc_get_product( $_product_id );
						$replaced_values = array(
							'%max_qty%'             => $max_qty,
							'%product_title%'       => $product->get_title(),
							'%qty_already_bought%'  => $user_already_bought,
							'%remaining_qty%'       => max( ( $max_qty - $user_already_bought ), 0 ),
						);
						$message = aiow_option( 'aiow_max_products_per_user_message',
						__( 'You can only buy maximum %max_qty% pcs. of %product_title% (you already bought %qty_already_bought% pcs.).', 'all-in-one-wc' ) );
						$message = str_replace( array_keys( $replaced_values ), $replaced_values, $message );
						if ( $is_cart ) {
							wc_print_notice( $message, 'notice' );
						} else {
							wc_add_notice( $message, 'error' );
						}
					} else {
						return false;
					}
				}
			}
			return $result;
		}
	}
}
