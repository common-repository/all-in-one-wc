<?php
/**
 * Shipping Module - Order Numbers.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Order_Numbers' ) ) {

	/**
	 * Declare class `Order_Numbers` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Order_Numbers extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'order_numbers';
			$this->short_desc = __( 'Order Numbers', 'all-in-one-wc' );
			$this->desc       = __( 'Sequential order numbering, custom order number prefix, suffix and number width. Prefix Options (Order Number Custom Prefix available in free version). Suffix options (Plus). ', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Sequential order numbering, custom order number prefix, suffix and number width.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-custom-order-numbers';
			parent::__construct();

			if ( $this->is_enabled() ) {
				// Add & display custom order number.
				add_action( 'wp_insert_post', array( $this, 'add_new_order_number' ), PHP_INT_MAX );
				add_filter( 'woocommerce_order_number', array( $this, 'display_order_number' ), PHP_INT_MAX, 2 );
				// Order tracking.
				if ( 'yes' === aiow_option( 'aiow_order_number_order_tracking_enabled', 'yes' ) ) {
					add_filter( 'woocommerce_shortcode_order_tracking_order_id', array( $this, 'add_order_number_to_tracking' ), PHP_INT_MAX );
					add_action( 'init', array( $this, 'remove_order_tracking_sanitize_order_id_filter' ) );
				}
				// Search by custom number.
				if ( 'yes' === aiow_option( 'aiow_order_number_search_by_custom_number_enabled', 'yes' ) ) {
					add_action( 'pre_get_posts', array( $this, 'search_by_custom_number' ) );
				}
				// "WooCommerce Subscriptions" plugin
				$woocommerce_subscriptions_types = array( 'subscription', 'renewal_order', 'resubscribe_order', 'copy_order' );
				foreach ( $woocommerce_subscriptions_types as $woocommerce_subscriptions_type ) {
					add_filter( 'wcs_' . $woocommerce_subscriptions_type . '_meta', array( $this, 'woocommerce_subscriptions_remove_meta_copy' ), PHP_INT_MAX, 3 );
				}
				// Editable order number.
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_order_number_editable_order_number_meta_box_enabled', 'no' ) ) ) {
					$this->meta_box_screen   = 'shop_order';
					$this->meta_box_context  = 'side';
					$this->meta_box_priority = 'high';
					add_action( 'add_meta_boxes',       array( $this, 'maybe_add_meta_box' ), PHP_INT_MAX, 2 );
					add_action( 'save_post_shop_order', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );
				}

				// Compatibility with WPNotif plugin.
				add_action( 'admin_init', function () {
					if ( 'yes' === aiow_option( 'aiow_order_numbers_compatibility_wpnotif', 'no' ) ) {
						remove_filter( 'wpnotif_filter_message', 'wpnotif_add_tracking_details', 10, 2 );
						add_filter( 'wpnotif_filter_message', array( $this, 'wpnotif_add_tracking_details' ), 10, 2 );
					}
				} );
			}
		}

		/**
		 * Tracking details.
		 *
		 * @param string $msg Message.
		 * @param object $order Order data.
		 * @return mixed
		 */
		function wpnotif_add_tracking_details( $msg, $order ) {
			$tracking_link_string = '';
			if ( class_exists( 'YITH_Tracking_Data' ) ) {
				$values            = array();
				$yith_placeholders = apply_filters( 'ywsn_sms_placeholders', array(), $order );
				if ( isset( $yith_placeholders['{tracking_url}'] ) ) {
					$tracking_link_string = $yith_placeholders['{tracking_url}'];
				}
			} else {
				$tracking_links = $this->wpnotif_get_wc_tracking_links( $order );
				if ( ! empty( $tracking_links ) ) {
					$tracking_link_string = implode( ',', $tracking_links );
				}
			}
			$msg = str_replace( '{{wc-tracking-link}}', $tracking_link_string, $msg );
			return $msg;
		}

		/**
		 * Tracking links.
		 *
		 * @param object $order Order data.
		 * @return array
		 */
		function wpnotif_get_wc_tracking_links( $order ) {
			if ( class_exists( 'WC_Shipment_Tracking_Actions' ) ) {
				$st = WC_Shipment_Tracking_Actions::get_instance();
			} else if ( is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) ) {
				$st = WC_Advanced_Shipment_Tracking_Actions::get_instance();
			} else {
				return array();
			}
			$order_id       = $order->get_id();
			$tracking_items = $st->get_tracking_items( $order_id );
			if ( ! empty( $tracking_items ) ) {
				$tracking_links = array();
				foreach ( $tracking_items as $tracking_item ) {
					$formated_item    = $st->get_formatted_tracking_item( $order_id, $tracking_item );
					$tracking_links[] = htmlspecialchars_decode( $formated_item['formatted_tracking_link'] );
					break;
				}
			}
			return $tracking_links;
		}

		/**
		 * Maybe add meta box.
		 *
		 * @param string $post_type Post type.
		 * @param object $post Post.
		 */
		function maybe_add_meta_box( $post_type, $post ) {
			if ( '' !== get_post_meta( $post->ID, '_aiow_order_number', true ) ) {
				parent::add_meta_box();
			}
		}

		/**
		 * WooCommerce subscriptions remove meta copy.
		 *
		 * @param array $meta Meta.
		 * @param mixed $to_order To order.
		 * @param mixed $from_order from order.
		 * @return array
		 */
		function woocommerce_subscriptions_remove_meta_copy( $meta, $to_order, $from_order ) {
			foreach ( $meta as $meta_id => $meta_item ) {
				if ( '_aiow_order_number' === $meta_item['meta_key'] ) {
					unset( $meta[ $meta_id ] );
				}
			}
			return $meta;
		}

		/**
		 * Remove order tracking sanitize order id filter.
		 */
		function remove_order_tracking_sanitize_order_id_filter() {
			remove_filter( 'woocommerce_shortcode_order_tracking_order_id', 'wc_sanitize_order_id' );
		}

		/**
		 * Search by custom number.
		 *
		 * @param object $query WP_Query.
		 * @return mixed
		 */
		function search_by_custom_number( $query ) {
			if (
				! is_admin() ||
				! property_exists( $query, "query" ) ||
				! isset( $query->query['s'] ) ||
				empty( $search = trim( $query->query['s'] ) ) ||
				'shop_order' !== $query->query['post_type']
			) {
				return;
			}
			remove_action( 'pre_get_posts', array( $this, 'search_by_custom_number' ) );

			// Get prefix and suffix options.
			$prefix = do_shortcode( aiow_option( 'aiow_order_number_prefix', '' ) );
			$prefix .= date_i18n( aiow_option( 'aiow_order_number_date_prefix', '' ) );
			$suffix = do_shortcode( aiow_option( 'aiow_order_number_suffix', '' ) );
			$suffix .= date_i18n( aiow_option( 'aiow_order_number_date_suffix', '' ) );

			// Ignore suffix and prefix from search input.
			$search_no_suffix            = preg_replace( "/\A{$prefix}/i", '', $search );
			$search_no_suffix_and_prefix = preg_replace( "/{$suffix}\z/i", '', $search_no_suffix );
			$final_search                = empty( $search_no_suffix_and_prefix ) ? $search : $search_no_suffix_and_prefix;

			// Post Status.
			$post_status = isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'any';

			// Try to search post by '_aiow_order_number' meta key.
			$meta_query_args = array(
				array(
					'key'     => '_aiow_order_number',
					'value'   => $final_search,
					'compare' => '='
				)
			);
			$search_query = new WP_Query( array(
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'post_status'            => $post_status,
				'post_type'              => 'shop_order',
				'meta_query'             => $meta_query_args
			) );

			// If found, create the query by meta_key.
			if ( 1 == $search_query->found_posts ) {
				$query->set( 'post_status', $post_status );
				$query->set( 's', '' );
				$query->set( 'post__in', array() );
				$current_meta_query = empty( $query->get( 'meta_query' ) ) ? array() : $query->get( 'meta_query' );
				$query->set( 'meta_query', array_merge( $current_meta_query, $meta_query_args ) );
			}
			else {
				$query->set( 'post_status', $post_status );
				$query->set( 's', '' );
				$current_post_in = empty( $query->get( 'post__in' ) ) ? array() : $query->get( 'post__in' );
				$query->set( 'post__in', array_merge( $current_post_in, array( $final_search ) ) );
			}
		}

		/**
		 * Add order number to tracking.
		 *
		 * @param string $order_number Order number.
		 * @return mixed
		 */
		function add_order_number_to_tracking( $order_number ) {
			$offset     = 0;
			$block_size = 512;
			while( true ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
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
					$_order        = wc_get_order( $_order_id );
					$_order_number = $this->display_order_number( $_order_id, $_order );
					if ( $_order_number === $order_number ) {
						return $_order_id;
					}
				}
				$offset += $block_size;
			}
			return $order_number;
		}

		/**
		 * Display order number.
		 *
		 * @param int    $order_number Order number.
		 * @param object $order Order data.
		 * @return int
		 */
		function display_order_number( $order_number, $order ) {
			$order_id = aiow_get_order_id( $order );
			$order_number_meta = get_post_meta( $order_id , '_aiow_order_number', true );
			if ( '' == $order_number_meta || 'no' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) ) {
				$order_number_meta = $order_id;
			}
			$order_timestamp = strtotime( ( AIOW_IS_WC_VERSION_BELOW_3 ? $order->post->post_date : $order->get_date_created() ) );
			$order_number = apply_filters( 'aiow_option',
				sprintf( '%s%s', do_shortcode( aiow_option( 'aiow_order_number_prefix', '' ) ), $order_number_meta ),
				sprintf( '%s%s%0' . aiow_option( 'aiow_order_number_min_width', 0 ) . 's%s%s',
					do_shortcode( aiow_option( 'aiow_order_number_prefix', '' ) ),
					date_i18n( aiow_option( 'aiow_order_number_date_prefix', '' ), $order_timestamp ),
					$order_number_meta,
					do_shortcode( aiow_option( 'aiow_order_number_suffix', '' ) ),
					date_i18n( aiow_option( 'aiow_order_number_date_suffix', '' ), $order_timestamp )
				)
			);
			if ( false !== strpos( $order_number, '%order_items_skus%' ) ) {
				$order_number = str_replace( '%order_items_skus%', do_shortcode( '[aiow_order_items order_id="' . $order_id . '" field="_sku" sep="-"]' ), $order_number );
			}
			return $order_number;
		}

		/**
		 * Add Renumerate Orders tool to WooCommerce menu (the content).
		 */
		function create_renumerate_orders_tool() {
			$result_message = '';
			if ( isset( $_POST['renumerate_orders'] ) ) {
				$this->renumerate_orders();
				$result_message = '<p><div class="updated"><p><strong>' . __( 'Orders successfully renumerated!', 'all-in-one-wc' ) . '</strong></p></div></p>';
			} else {
				if ( 'yes' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) ) {
					$result_message .= '<p>' . sprintf( __( 'Sequential number generation is enabled. Next order number will be %s.', 'all-in-one-wc' ),
						'<code>' . aiow_option( 'aiow_order_number_counter', 1 ) . '</code>' ) . '</p>';
				}
			}
			$html = '';
			$html .= '<div class="wrap">';
			$html .= $this->get_tool_header_html( 'renumerate_orders' );
			$html .= '<p>';
			$html .= sprintf(
				__( 'Press the button below to renumerate all existing orders starting from order counter settings in <a href="%s">Order Numbers</a> module.',
					'all-in-one-wc' ),
				admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=shipping_and_orders&section=order_numbers' )
			);
			$html .= '</p>';
			$html .= $result_message;
			$html .= '<form method="post" action="">';
			$html .= '<input class="button-primary" type="submit" name="renumerate_orders" value="' . __( 'Renumerate orders', 'all-in-one-wc' ) . '">';
			$html .= '</form>';
			$html .= '</div>';
			echo $html;
		}

		/**
		 * Add new order number.
		 *
		 * @param int $order_id Order ID.
		 */
		function add_new_order_number( $order_id ) {
			$this->add_order_number_meta( $order_id, false );
		}

		/**
		 * Maybe reset sequential counter.
		 *
		 * @param int $current_order_number Current order number.
		 * @param int $order_id Order ID.
		 * @return int
		 */
		function maybe_reset_sequential_counter( $current_order_number, $order_id ) {
			if ( 'no' != ( $reset_period = aiow_option( 'aiow_order_number_counter_reset_enabled', 'no' ) ) ) {
				$previous_order_date = aiow_option( 'aiow_order_number_counter_previous_order_date', 0 );
				$current_order_date  = strtotime( aiow_get_order_date( wc_get_order( $order_id ) ) );
				update_option( 'aiow_order_number_counter_previous_order_date', $current_order_date );
				if ( 0 != $previous_order_date ) {
					$do_reset = false;
					switch ( $reset_period ) {
						case 'daily':
							$do_reset = (
								date( 'Y', $current_order_date ) != date( 'Y', $previous_order_date ) ||
								date( 'm', $current_order_date ) != date( 'm', $previous_order_date ) ||
								date( 'd', $current_order_date ) != date( 'd', $previous_order_date )
							);
							break;
						case 'monthly':
							$do_reset = (
								date( 'Y', $current_order_date ) != date( 'Y', $previous_order_date ) ||
								date( 'm', $current_order_date ) != date( 'm', $previous_order_date )
							);
							break;
						case 'yearly':
							$do_reset = (
								date( 'Y', $current_order_date ) != date( 'Y', $previous_order_date )
							);
							break;
					}
					if ( $do_reset ) {
						return 1;
					}
				}
			}
			return $current_order_number;
		}

		/**
		 * Add/update order_number meta to order.
		 *
		 * @param int  $order_id Order ID.
		 * @param bool $do_overwrite Do overwrite.
		 * @return mixed
		 */
		function add_order_number_meta( $order_id, $do_overwrite ) {
			if ( 'shop_order' !== get_post_type( $order_id ) || 'auto-draft' === get_post_status( $order_id ) ) {
				return;
			}
			if ( true === $do_overwrite || 0 == get_post_meta( $order_id, '_aiow_order_number', true ) ) {
				if ( $order_id < aiow_option( 'aiow_order_numbers_min_order_id', 0 ) ) {
					return;
				}
				if ( 'yes' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) && 'yes' === aiow_option( 'aiow_order_number_use_mysql_transaction_enabled', 'yes' ) ) {
					global $wpdb;
					$wpdb->query( 'START TRANSACTION' );
					$wp_options_table = $wpdb->prefix . 'options';
					$result_select = $wpdb->get_row( "SELECT * FROM $wp_options_table WHERE option_name = 'aiow_order_number_counter'" );
					if ( NULL != $result_select ) {
						$current_order_number = $this->maybe_reset_sequential_counter( $result_select->option_value, $order_id );
						$result_update = $wpdb->update( $wp_options_table, array( 'option_value' => ( $current_order_number + 1 ) ), array( 'option_name' => 'aiow_order_number_counter' ) );
						if ( NULL != $result_update || $result_select->option_value == ( $current_order_number + 1 ) ) {
							$wpdb->query( 'COMMIT' );
							update_post_meta( $order_id, '_aiow_order_number', apply_filters( 'aiow_order_number_meta', $current_order_number, $order_id ) );
						} else {
							$wpdb->query( 'ROLLBACK' );
						}
					} else {
						$wpdb->query( 'ROLLBACK' );
					}
				} else {
					if ( 'hash_crc32' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) ) {
						$current_order_number = sprintf( "%u", crc32( $order_id ) );
					} elseif ( 'yes' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) ) {
						$current_order_number = $this->maybe_reset_sequential_counter( aiow_option( 'aiow_order_number_counter', 1 ), $order_id );
						update_option( 'aiow_order_number_counter', ( $current_order_number + 1 ) );
					} else { // 'no' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) // order ID
						$current_order_number = '';
					}
					update_post_meta( $order_id, '_aiow_order_number', apply_filters( 'aiow_order_number_meta', $current_order_number, $order_id ) );
				}
			}
		}

		/**
		 * Renumerate orders function.
		 */
		function renumerate_orders() {
			if ( 'yes' === aiow_option( 'aiow_order_number_sequential_enabled', 'yes' ) && 'no' != aiow_option( 'aiow_order_number_counter_reset_enabled', 'no' ) ) {
				update_option( 'aiow_order_number_counter_previous_order_date', 0 );
			}
			$offset     = 0;
			$block_size = 512;
			while( true ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => $block_size,
					'orderby'        => aiow_option( 'aiow_order_numbers_renumerate_tool_orderby', 'date' ),
					'order'          => aiow_option( 'aiow_order_numbers_renumerate_tool_order',   'ASC' ),
					'offset'         => $offset,
					'fields'         => 'ids',
				);
				$loop = new \WP_Query( $args );
				if ( ! $loop->have_posts() ) {
					break;
				}
				foreach ( $loop->posts as $order_id ) {
					$this->add_order_number_meta( $order_id, true );
				}
				$offset += $block_size;
			}
		}
	}
}
