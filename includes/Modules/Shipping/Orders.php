<?php
/**
 * Shipping Module - Orders.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Orders' ) ) {

	/**
	 * Declare class `Orders` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Orders extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id         = 'orders';
			$this->short_desc = __( 'Orders', 'all-in-one-wc' );
			$this->desc       = __( 'Orders auto-complete; admin order currency; admin order navigation; bulk regenerate download permissions for orders (Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Orders auto-complete; admin order currency; admin order navigation; bulk regenerate download permissions for orders.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-orders';
			parent::__construct();

			if ( $this->is_enabled() ) {
				// Order auto complete.
				if ( 'yes' === aiow_option( 'aiow_order_auto_complete_enabled', 'no' ) ) {
					add_action( 'woocommerce_thankyou',         array( $this, 'auto_complete_order' ), PHP_INT_MAX );
					add_action( 'woocommerce_payment_complete', array( $this, 'auto_complete_order' ), PHP_INT_MAX );
				}
				// Order currency.
				if ( 'yes' === aiow_option( 'aiow_order_admin_currency', 'no' ) ) {
					$this->meta_box_screen = 'shop_order';
					add_action( 'add_meta_boxes',       array( $this, 'add_meta_box' ) );
					add_action( 'save_post_shop_order', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );
					if ( 'filter' === aiow_option( 'aiow_order_admin_currency_method', 'filter' ) ) {
						$woocommerce_get_order_currency_filter = ( aiow_IS_WC_VERSION_BELOW_3 ? 'woocommerce_get_order_currency' : 'woocommerce_order_get_currency' );
						add_filter( $woocommerce_get_order_currency_filter, array( $this, 'change_order_currency' ), PHP_INT_MAX, 2 );
					}
				}

				// Bulk Regenerate Download Permissions.
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_order_bulk_regenerate_download_permissions_enabled', 'no' ) ) ) {
					// Actions
					if ( 'yes' === aiow_option( 'aiow_order_bulk_regenerate_download_permissions_actions', 'no' ) ) {
						add_filter( 'bulk_actions-edit-shop_order',        array( $this, 'register_bulk_actions_regenerate_download_permissions' ), PHP_INT_MAX );
						add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions_regenerate_download_permissions' ), 10, 3 );
					}
					// All orders.
					add_action( 'woojetpack_after_settings_save', array( $this, 'maybe_bulk_regenerate_download_permissions_all_orders' ) );
					// Admin notices.
					add_filter( 'admin_notices', array( $this, 'admin_notice_regenerate_download_permissions' ) );
					// All orders - Cron.
					if ( 'disabled' != apply_filters( 'aiow_option', 'disabled', aiow_option( 'aiow_order_bulk_regenerate_download_permissions_all_orders_cron', 'disabled' ) ) ) {
						add_action( 'init',       array( $this, 'schedule_bulk_regenerate_download_permissions_all_orders_cron' ) );
						add_action( 'admin_init', array( $this, 'schedule_bulk_regenerate_download_permissions_all_orders_cron' ) );
						add_filter( 'cron_schedules', 'aiow_crons_add_custom_intervals' );
						add_action( 'aiow_bulk_regenerate_download_permissions_all_orders_cron', array( $this, 'bulk_regenerate_download_permissions_all_orders' ) );
					}
				}

				// Country by IP.
				if ( 'yes' === aiow_option( 'aiow_orders_country_by_ip_enabled', 'no' ) ) {
					add_action( 'add_meta_boxes', array( $this, 'add_country_by_ip_meta_box' ) );
				}

				// Orders navigation.
				if ( 'yes' === aiow_option( 'aiow_orders_navigation_enabled', 'no' ) ) {
					add_action( 'add_meta_boxes', array( $this, 'add_orders_navigation_meta_box' ) );
					add_action( 'admin_init',     array( $this, 'handle_orders_navigation' ) );
				}

				// Editable orders.
				if ( 'yes' === aiow_option( 'aiow_orders_editable_status_enabled', 'no' ) ) {
					add_filter( 'wc_order_is_editable', array( $this, 'editable_status' ), PHP_INT_MAX, 2 );
				}
			}
		}

		/**
		 * Editable status OR not.
		 *
		 * @param bool   $is_editable Status editable.
		 * @param object $order Order.
		 * @return bool
		 */
		function editable_status( $is_editable, $order ) {
			return in_array( $order->get_status(), aiow_option( 'aiow_orders_editable_status', array( 'pending', 'on-hold', 'auto-draft' ) ), true );
		}

		/**
		 * Orders navigation.
		 */
		function handle_orders_navigation() {
			if ( isset( $_GET['aiow_orders_navigation'] ) ) {
				$url = ( ! isset( $_GET['post'] ) || false === ( $adjacent_order_id = aiow_get_adjacent_order_id( $_GET['post'], $_GET['aiow_orders_navigation'] ) ) ?
					remove_query_arg( 'aiow_orders_navigation' ) :
					admin_url( 'post.php?post=' . $adjacent_order_id . '&action=edit' ) );
				wp_safe_redirect( $url );
				exit;
			}
		}

		/**
		 * Add orders navigation meta box.
		 */
		function add_orders_navigation_meta_box() {
			add_meta_box(
				'wc-jetpack-' . $this->id . '-navigation',
				 __( 'Order Navigation', 'all-in-one-wc' ),
				array( $this, 'create_orders_navigation_meta_box' ),
				'shop_order',
				'side',
				'high'
			);
		}

		/**
		 * Create orders navigation meta box.
		 */
		function create_orders_navigation_meta_box() {
			echo '<a href="' . add_query_arg( 'aiow_orders_navigation', 'prev' ) . '">' . '&lt;&lt; ' . __( 'Previous order', 'all-in-one-wc' ) . '</a>' .
				 '<a href="' . add_query_arg( 'aiow_orders_navigation', 'next' ) . '" style="float:right;">' . __( 'Next order', 'all-in-one-wc' ) . ' &gt;&gt;' . '</a>';
		}

		/**
		 * Add country by ip meta box.
		 */
		function add_country_by_ip_meta_box() {
			add_meta_box(
				'wc-jetpack-' . $this->id . '-country-by-ip',
				 __( 'Country by IP', 'all-in-one-wc' ),
				array( $this, 'create_country_by_ip_meta_box' ),
				'shop_order',
				'side',
				'low'
			);
		}

		/**
		 * Create country by ip meta box.
		 */
		function create_country_by_ip_meta_box() {
			if (
				class_exists( 'WC_Geolocation' ) &&
				( $order = wc_get_order() ) &&
				( $customer_ip = $order->get_customer_ip_address() ) &&
				( $location = WC_Geolocation::geolocate_ip( $customer_ip ) ) &&
				isset( $location['country'] ) && '' != $location['country']
			) {
				echo aiow_get_country_flag_by_code( $location['country'] ) . ' ' .
					aiow_get_country_name_by_code( $location['country'] ) .
					' (' . $location['country'] . ')' .
					' [' . $customer_ip . ']';
			} else {
				echo '<em>' . __( 'No data.', 'all-in-one-wc' ) . '</em>';
			}
		}

		/**
		 * Schedule bulk regenerate download permissions all orders cron.
		 */
		function schedule_bulk_regenerate_download_permissions_all_orders_cron() {
			aiow_crons_schedule_the_events(
				'aiow_bulk_regenerate_download_permissions_all_orders_cron',
				apply_filters( 'aiow_option', 'disabled', aiow_option( 'aiow_order_bulk_regenerate_download_permissions_all_orders_cron', 'disabled' ) )
			);
		}

		/**
		 * Handle bulk actions regenerate download permissions.
		 *
		 * @param string $redirect_to Redirect URL.
		 * @param string $doaction Do action.
		 * @param int    $post_ids Post ID.
		 * @return string
		 */
		function handle_bulk_actions_regenerate_download_permissions( $redirect_to, $doaction, $post_ids ) {
			if ( $doaction !== 'aiow_regenerate_download_permissions' ) {
				return $redirect_to;
			}
			$data_store = WC_Data_Store::load( 'customer-download' );
			foreach ( $post_ids as $post_id ) {
				$data_store->delete_by_order_id( $post_id );
				wc_downloadable_product_permissions( $post_id, true );
			}
			$redirect_to = add_query_arg( 'aiow_bulk_regenerated_download_permissions', count( $post_ids ), $redirect_to );
			return $redirect_to;
		}

		/**
		 * Register bulk actions regenerate download permissions.
		 *
		 * @param array $bulk_actions Bluk actions.
		 * @return array
		 */
		function register_bulk_actions_regenerate_download_permissions( $bulk_actions ) {
			$bulk_actions['aiow_regenerate_download_permissions'] = __( 'Regenerate download permissions', 'all-in-one-wc' );
			return $bulk_actions;
		}

		/**
		 * Admin notice regenerate download permissions.
		 */
		function admin_notice_regenerate_download_permissions() {
			if ( ! empty( $_REQUEST['aiow_bulk_regenerated_download_permissions'] ) ) {
				$orders_count = intval( $_REQUEST['aiow_bulk_regenerated_download_permissions'] );
				$message = sprintf(
					_n( 'Download permissions regenerated for %s order.', 'Download permissions regenerated for %s orders.', $orders_count, 'all-in-one-wc' ),
					'<strong>' . $orders_count . '</strong>'
				);
				echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
			}
		}

		/**
		 * Bulk regenerate download permissions all orders.
		 *
		 * @return string
		 */
		function bulk_regenerate_download_permissions_all_orders() {
			$data_store   = WC_Data_Store::load( 'customer-download' );
			$block_size   = 512;
			$offset       = 0;
			$total_orders = 0;
			while( true ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post_status'    => 'any',
					'posts_per_page' => $block_size,
					'offset'         => $offset,
					'orderby'        => 'ID',
					'order'          => 'DESC',
					'fields'         => 'ids',
				);
				$loop = new WP_Query( $args );
				if ( ! $loop->have_posts() ) {
					break;
				}
				foreach ( $loop->posts as $post_id ) {
					$data_store->delete_by_order_id( $post_id );
					wc_downloadable_product_permissions( $post_id, true );
					$total_orders++;
				}
				$offset += $block_size;
			}
			return $total_orders;
		}

		/**
		 * Maybe bulk regenerate download permissions all orders.
		 */
		function maybe_bulk_regenerate_download_permissions_all_orders() {
			if ( 'yes' === aiow_option( 'aiow_order_bulk_regenerate_download_permissions_all_orders', 'no' ) ) {
				update_option( 'aiow_order_bulk_regenerate_download_permissions_all_orders', 'no' );
				$total_orders = $this->bulk_regenerate_download_permissions_all_orders();
				wp_safe_redirect( add_query_arg( 'aiow_bulk_regenerated_download_permissions', $total_orders ) );
				exit;
			}
		}

		/**
		 * Change order currency.
		 *
		 * @param string $order_currency Currency.
		 * @param object $_order Order data.
		 */
		function change_order_currency( $order_currency, $_order ) {
			return ( '' != ( $aiow_order_currency = get_post_meta( aiow_get_order_id( $_order ), '_' . 'aiow_order_currency', true ) ) ) ? $aiow_order_currency : $order_currency;
		}

		/**
		 * Auto Complete all WooCommerce orders.
		 *
		 * @param int $order_id Order ID.
		 * @return null
		 */
		function auto_complete_order( $order_id ) {
			if ( ! $order_id ) {
				return;
			}
			$order = wc_get_order( $order_id );
			$payment_methods = apply_filters( 'aiow_option', '', aiow_option( 'aiow_order_auto_complete_payment_methods', array() ) );
			if ( ! empty( $payment_methods ) && ! in_array( $order->get_payment_method(), $payment_methods ) ) {
				return;
			}
			$order->update_status( 'completed' );
		}
	}
}
