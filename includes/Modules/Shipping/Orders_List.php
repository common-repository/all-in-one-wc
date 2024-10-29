<?php
/**
 * Shipping Module - Admin Orders List.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Orders_List' ) ) {

	/**
	 * Declare class `Orders_List` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Orders_List extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'admin_orders_list';
			$this->short_desc = __( 'Admin Orders List', 'all-in-one-wc' );
			$this->desc       = __( 'Customize admin orders list: add custom columns (1 allowed in free version); add multiple status filtering (1 allowed in free version).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Customize admin orders list: add custom columns; add multiple status filtering.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-admin-orders-list';
			parent::__construct();

			if ( $this->is_enabled() ) {
				// Custom columns.
				if ( 'yes' === aiow_option( 'aiow_order_admin_list_custom_columns_enabled', 'no' ) ) {
					add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_columns' ), PHP_INT_MAX - 1 );
					add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_order_column' ), PHP_INT_MAX );
					if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_country', 'no' ) || 'yes' === aiow_option( 'aiow_orders_list_custom_columns_currency', 'no' ) ) {
						// Billing country or Currency filtering.
						add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
						add_filter( 'parse_query', array( $this, 'parse_query' ) );
					}
					// Maybe make sortable custom columns.
					add_filter( 'manage_edit-shop_order_sortable_columns',  array( $this, 'shop_order_sortable_columns' ) );
					add_action( 'pre_get_posts', array( $this, 'shop_order_pre_get_posts_order_by_column' ) );
				}

				// Multiple status.
				if ( 'yes' === aiow_option( 'aiow_order_admin_list_multiple_status_enabled', 'no' ) ) {
					if ( 'yes' === aiow_option( 'aiow_order_admin_list_multiple_status_not_completed_link', 'no' ) ) {
						add_filter( 'views_edit-shop_order', array( $this, 'add_shop_order_multiple_statuses_not_completed_link' ) );
						add_action( 'pre_get_posts', array( $this, 'filter_shop_order_multiple_statuses_not_completed_link' ), PHP_INT_MAX, 1 );
					}
					if ( 'no' != aiow_option( 'aiow_order_admin_list_multiple_status_filter', 'no' ) ) {
						add_action( 'restrict_manage_posts', array( $this, 'add_shop_order_multiple_statuses' ), PHP_INT_MAX, 2 );
						add_action( 'pre_get_posts', array( $this, 'filter_shop_order_multiple_statuses' ), PHP_INT_MAX, 1 );
					}
					if ( 'yes' === aiow_option( 'aiow_order_admin_list_hide_default_statuses_menu', 'no' ) ) {
						add_action( 'admin_head', array( $this, 'hide_default_statuses_menu' ), PHP_INT_MAX );
					}
					if ( 'yes' === aiow_option( 'aiow_order_admin_list_multiple_status_admin_menu', 'no' ) ) {
						add_action( 'admin_menu', array( $this, 'admin_menu_multiple_status' ) );
					}
				}

				// Columns Order.
				if ( 'yes' === aiow_option( 'aiow_order_admin_list_columns_order_enabled', 'no' ) ) {
					add_filter( 'manage_edit-shop_order_columns', array( $this, 'rearange_order_columns' ), PHP_INT_MAX - 1 );
				}
			}
		}

		/**
		 * Add admin menu multiple status.
		 */
		function admin_menu_multiple_status() {
			// Remove "Coupons" menu (to get "Orders" menus on top).
			$coupons_menu = remove_submenu_page( 'woocommerce', 'edit.php?post_type=shop_coupon' );
			// Maybe remove original "Orders" menu.
			if ( 'yes' === aiow_option( 'aiow_order_admin_list_multiple_status_admin_menu_remove_original', 'no' ) ) {
				remove_submenu_page( 'woocommerce', 'edit.php?post_type=shop_order' );
			}
			// Add presets.
			$titles       = aiow_option( 'aiow_order_admin_list_multiple_status_presets_titles',   array() );
			$statuses     = aiow_option( 'aiow_order_admin_list_multiple_status_presets_statuses', array() );
			$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_order_admin_list_multiple_status_presets_total_number', 1 ) );
			for ( $i = 1; $i <= $total_number; $i++ ) {
				if ( ! empty( $titles[ $i ] ) && ! empty( $statuses[ $i ] ) ) {
					$menu_slug = 'edit.php?post_type=shop_order';
					foreach ( $statuses[ $i ] as $x => $status ) {
						$menu_slug .= "&aiow_admin_filter_statuses[{$x}]={$status}";
					}
					$orders_count_html = '';
					if ( 'yes' === aiow_option( 'aiow_order_admin_list_multiple_status_admin_menu_counter', 'no' ) ) {
						$order_count = 0;
						foreach ( $statuses[ $i ] as $x => $status ) {
							$order_count += wc_orders_count( substr( $status, 3 ) );
						}
						$orders_count_html = ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . ' aiow-order-count-wrapper"><span class="aiow-order-count">' . number_format_i18n( $order_count ) . '</span></span>';
					}
					add_submenu_page( 'woocommerce', $titles[ $i ], $titles[ $i ] . $orders_count_html, 'edit_shop_orders', $menu_slug );
				}
			}
			// Re-add "Coupons" menu.
			add_submenu_page( 'woocommerce', $coupons_menu[0], $coupons_menu[3], $coupons_menu[1], $coupons_menu[2] );
		}

		/**
		 * Shop order pre query posts.
		 *
		 * @param object $query WP_Query.
		 */
		function shop_order_pre_get_posts_order_by_column( $query ) {
			if (
				$query->is_main_query() &&
				( $orderby = $query->get( 'orderby' ) ) &&
				isset( $query->query['post_type'] ) && 'shop_order' === $query->query['post_type'] &&
				isset( $query->is_admin ) && 1 == $query->is_admin
			) {
				if ( 'aiow_orders_custom_column_' === substr( $orderby, 0, 25 ) ) {
					$index = substr( $orderby, 25 );
					$query->set( 'orderby', aiow_option( 'aiow_orders_list_custom_columns_sortable_' . $index, 'no' ) );
					$query->set( 'meta_key', aiow_option( 'aiow_orders_list_custom_columns_sortable_key_' . $index, '' ) );
				}
			}
		}

		/**
		 * Make columns sortable.
		 *
		 * @param array $columns WP_Table columns.
		 * @return array
		 */
		function shop_order_sortable_columns( $columns ) {
			$custom = array();
			$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_orders_list_custom_columns_total_number', 1 ) );
			for ( $i = 1; $i <= $total_number; $i++ ) {
				if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_enabled_' . $i, 'no' ) ) {
					if ( 'no' != aiow_option( 'aiow_orders_list_custom_columns_sortable_' . $i, 'no' ) && '' != aiow_option( 'aiow_orders_list_custom_columns_sortable_key_' . $i, '' ) ) {
						$custom[ 'aiow_orders_custom_column_' . $i ] = 'aiow_orders_custom_column_' . $i;
					}
				}
			}
			return ( ! empty( $custom ) ? wp_parse_args( $custom, $columns ) : $columns );
		}

		/**
		 * Hide default statuses menu.
		 */
		function hide_default_statuses_menu() {
			echo '<style>body.post-type-shop_order ul.subsubsub {display: none !important;}</style>';
		}

		/**
		 * Get orders default columns in order.
		 *
		 * @return string
		 */
		function get_orders_default_columns_in_order() {
			$columns = array(
				'cb',
				'order_status',
				'order_title',
				'order_items',
				'billing_address',
				'shipping_address',
				'customer_message',
				'order_notes',
				'order_date',
				'order_total',
				'order_actions',
			);
			return implode( PHP_EOL, $columns );
		}

		/**
		 * Add shop order multiple statuses not completed link.
		 *
		 * @param array $views Screen views.
		 * @return array
		 */
		function add_shop_order_multiple_statuses_not_completed_link( $views ) {
			global $wp_query;
			if ( ! aiow_current_user_can( 'edit_others_pages' ) ) {
				return $views;
			}
			$all_not_completed_statuses          = wc_get_order_statuses();
			unset( $all_not_completed_statuses['wc-completed'] );
			$all_not_completed_statuses          = array_keys( $all_not_completed_statuses );
			$all_not_completed_statuses_param    = urlencode( implode( ',', $all_not_completed_statuses ) );
			$class                               = ( isset( $wp_query->query['post_status'] ) && is_array( $wp_query->query['post_status'] ) && $all_not_completed_statuses === $wp_query->query['post_status'] ) ? 'current' : '';
			$query_string                        = remove_query_arg( array( 'post_status', 'aiow_admin_filter_statuses' ) );
			$query_string                        = add_query_arg( 'post_status', $all_not_completed_statuses_param, $query_string );
			$views['aiow_statuses_not_completed'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . __( 'Not Completed', 'all-in-one-wc' ) . '</a>';
			return $views;
		}

		/**
		 * Filter shop order multiple statuses not completed link.
		 *
		 * @param object $query WP_Query.
		 */
		function filter_shop_order_multiple_statuses_not_completed_link( $query ) {
			if ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-admin/edit.php' ) && isset( $_GET['post_type'] ) && 'shop_order' === $_GET['post_type'] ) {
				if ( aiow_current_user_can( 'edit_others_pages' ) ) {
					if ( isset( $_GET['post_status'] ) && false !== strpos( $_GET['post_status'], ',' ) ) {
						$post_statuses = explode( ',', $_GET['post_status'] );
						$query->query['post_status']      = $post_statuses;
						$query->query_vars['post_status'] = $post_statuses;
					}
				}
			}
		}

		/**
		 * Multiple shop order statuses.
		 *
		 * @param string $type Column type.
		 * @return string
		 */
		function multiple_shop_order_statuses( $type ) {
			$checked_post_statuses = isset( $_GET['aiow_admin_filter_statuses'] ) ? $_GET['aiow_admin_filter_statuses'] : array();
			$html = '';
			$html .= ( 'checkboxes' === $type ) ?
				'<span id="aiow_admin_filter_shop_order_statuses">' :
				'<select multiple name="aiow_admin_filter_statuses[]" id="aiow_admin_filter_shop_order_statuses" class="chosen_select">';
			$num_posts = wp_count_posts( 'shop_order', 'readable' );
			foreach ( array_merge( wc_get_order_statuses(), array( 'trash' => __( 'Trash', 'all-in-one-wc' ) ) ) as $status_id => $status_title ) {
				$total_number = ( isset( $num_posts->{$status_id} ) ) ? $num_posts->{$status_id} : 0;
				if ( $total_number > 0 ) {
					$html .= ( 'checkboxes' === $type ) ?
						'<input type="checkbox" name="aiow_admin_filter_statuses[]" style="width:16px;height:16px;" value="' . $status_id . '"' .
							checked( in_array( $status_id, $checked_post_statuses ), true, false ) . '>' . $status_title . ' (' . $total_number . ') ' :
						'<option value="' . $status_id . '"' . selected( in_array( $status_id, $checked_post_statuses ), true, false ) . '>' .
							$status_title . ' (' . $total_number . ') ' . '</option>';
				}
			}
			$html .= ( 'checkboxes' === $type ) ?
				'</span>' :
				'</select>';
			return $html;
		}

		/**
		 * Add shop order multiple statuses.
		 *
		 * @param string $post_type Post type name.
		 * @param string $which Position.
		 */
		function add_shop_order_multiple_statuses( $post_type, $which ) {
			if ( 'shop_order' === $post_type ) {
				echo $this->multiple_shop_order_statuses( aiow_option( 'aiow_order_admin_list_multiple_status_filter', 'no' ) );
			}
		}

		/**
		 * Filter shop order multiple statuses.
		 *
		 * @param object $query WP_Query.
		 */
		function filter_shop_order_multiple_statuses( $query ) {
			if ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-admin/edit.php' ) && isset( $_GET['post_type'] ) && 'shop_order' === $_GET['post_type'] ) {
				if ( aiow_current_user_can( 'edit_others_pages' ) ) {
					if ( isset( $_GET['aiow_admin_filter_statuses'] ) ) {
						$post_statuses = $_GET['aiow_admin_filter_statuses'];
						$query->query['post_status']      = $post_statuses;
						$query->query_vars['post_status'] = $post_statuses;
					}
				}
			}
		}

		/**
		 * Filter the orders in admin based on options.
		 *
		 * @param object $query WP_Query.
		 * @return mixed
		 */
		function parse_query( $query ) {
			global $typenow, $wp_query;
			if ( $typenow != 'shop_order' ) {
				return;
			}
			if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_country', 'no' ) && isset( $_GET['country'] ) && 'all' != $_GET['country'] ) {
				$query->query_vars['meta_query'][] = array(
					'key'   => '_billing_country',
					'value' => $_GET['country'],
				);
			}
			if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_currency', 'no' ) && isset( $_GET['currency'] ) && 'all' != $_GET['currency'] ) {
				$query->query_vars['meta_query'][] = array(
					'key'   => '_order_currency',
					'value' => $_GET['currency'],
				);
			}
		}

		/**
		 * Filters for post types.
		 */
		function restrict_manage_posts() {
			global $typenow, $wp_query;
			if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
				if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_country', 'no' ) ) {
					$selected_coutry = isset( $_GET['country'] ) ? $_GET['country'] : 'all';
					$countries = array_merge( array( 'all' => __( 'All countries', 'all-in-one-wc' ) ), aiow_get_countries() );
					echo '<select id="country" name="country">';
					foreach ( $countries as $code => $name ) {
						echo '<option value="' . $code . '" ' . selected( $code, $selected_coutry, false ) . '>' . $name . '</option>';
					}
					echo '</select>';
				}
				if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_currency', 'no' ) ) {
					$selected_currency = isset( $_GET['currency'] ) ? $_GET['currency'] : 'all';
					$currencies = array_merge( array( 'all' => __( 'All currencies', 'all-in-one-wc' ) ), aiow_get_woocommerce_currencies_and_symbols() );
					echo '<select id="currency" name="currency">';
					foreach ( $currencies as $code => $name ) {
						echo '<option value="' . $code . '" ' . selected( $code, $selected_currency, false ) . '>' . $name . '</option>';
					}
					echo '</select>';
				}
			}
		}

		/**
		 * Rearange order columns.
		 *
		 * @param array $columns WP_Table columns.
		 * @return array
		 */
		function rearange_order_columns( $columns ) {
			$reordered_columns = aiow_option( 'aiow_order_admin_list_columns_order', $this->get_orders_default_columns_in_order() );
			$reordered_columns = explode( PHP_EOL, $reordered_columns );
			$reordered_columns_result = array();
			if ( ! empty( $reordered_columns ) ) {
				foreach ( $reordered_columns as $column_id ) {
					$column_id = str_replace( "\n", '', $column_id );
					$column_id = str_replace( "\r", '', $column_id );
					if ( '' != $column_id && isset( $columns[ $column_id ] ) ) {
						$reordered_columns_result[ $column_id ] = $columns[ $column_id ];
						unset( $columns[ $column_id ] );
					}
				}
			}
			return array_merge( $reordered_columns_result, $columns );
		}

		/**
		 * Add order columns.
		 *
		 * @param array $columns WP_Table columns.
		 * @return array
		 */
		function add_order_columns( $columns ) {
			if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_country', 'no' ) ) {
				$columns['country'] = __( 'Billing Country', 'all-in-one-wc' );
			}
			if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_currency', 'no' ) ) {
				$columns['currency'] = __( 'Currency Code', 'all-in-one-wc' );
			}
			$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_orders_list_custom_columns_total_number', 1 ) );
			for ( $i = 1; $i <= $total_number; $i++ ) {
				if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_enabled_' . $i, 'no' ) ) {
					$columns[ 'aiow_orders_custom_column_' . $i ] = aiow_option( 'aiow_orders_list_custom_columns_label_' . $i, '' );
				}
			}
			return $columns;
		}

		/**
		 * Output custom columns for orders
		 *
		 * @param string $column currnent column.
		 */
		function render_order_column( $column ) {
			if ( 'country' === $column && 'yes' === aiow_option( 'aiow_orders_list_custom_columns_country', 'no' ) ) {
				$country_code = do_shortcode( '[aiow_order_checkout_field field_id="billing_country"]' );
				echo ( 2 == strlen( $country_code ) )
					? aiow_get_country_flag_by_code( $country_code ) . ' ' . aiow_get_country_name_by_code( $country_code )
					: aiow_get_country_name_by_code( $country_code );
			} elseif ( 'currency' === $column && 'yes' === aiow_option( 'aiow_orders_list_custom_columns_currency', 'no' ) ) {
				echo do_shortcode( '[aiow_order_currency]' );
			} else {
				$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_orders_list_custom_columns_total_number', 1 ) );
				for ( $i = 1; $i <= $total_number; $i++ ) {
					if ( 'yes' === aiow_option( 'aiow_orders_list_custom_columns_enabled_' . $i, 'no' ) ) {
						if ( 'aiow_orders_custom_column_' . $i === $column ) {
							echo do_shortcode( aiow_option( 'aiow_orders_list_custom_columns_value_' . $i, '' ) );
						}
					}
				}
			}
		}
	}
}
