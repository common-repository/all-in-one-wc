<?php
/**
 * Shipping Module - Order Custom Statuses.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Custom_Order_Statuses' ) ) {

	/**
	 * Declare class `Custom_Order_Statuses` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Custom_Order_Statuses extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id         = 'order_custom_statuses';
			$this->short_desc = __( 'Order Custom Statuses', 'all-in-one-wc' );
			$this->desc       = __( 'Custom statuses for WooCommerce orders. Make Custom Status Orders Editable (Plus). Add Custom Statuses to Admin Order List Action Buttons (Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Custom statuses for WooCommerce orders.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-order-custom-statuses';
			parent::__construct();

			$this->add_tools( array(
				'custom_statuses' => array(
					'title' => __( 'Custom Statuses', 'all-in-one-wc' ),
					'desc'  => __( 'Tool lets you add, edit or delete any custom status for WooCommerce orders.', 'all-in-one-wc' ),
				),
			) );

			if ( $this->is_enabled() ) {
				// WooCommerce statuses.
				add_filter( 'wc_order_statuses', array( $this, 'add_custom_statuses_to_filter' ), PHP_INT_MAX );
				if ( 'no' === aiow_option( 'aiow_load_modules_on_init', 'no' ) ) {
					add_action( 'init', array( $this, 'register_custom_post_statuses' ) );
				} else {
					$this->register_custom_post_statuses();
				}

				// Load style.
				add_action( 'admin_head', array( $this, 'hook_statuses_icons_css' ) );
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_orders_custom_statuses_column_colored', 'no' ) ) ) {
					add_action( 'admin_head', array( $this, 'hook_statuses_column_css' ) );
				}

				// Default order status.
				add_filter( 'woocommerce_default_order_status', array( $this, 'set_default_order_status' ), PHP_INT_MAX );

				// Add custom statuses to admin reports.
				if ( 'yes' === aiow_option( 'aiow_orders_custom_statuses_add_to_reports' ) ) {
					add_filter( 'woocommerce_reports_order_statuses', array( $this, 'add_custom_order_statuses_to_reports' ), PHP_INT_MAX );
				}

				// Add all statuses to admin order bulk actions.
				if ( 'yes' === aiow_option( 'aiow_orders_custom_statuses_add_to_bulk_actions' ) ) {
					add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 11 );
				}

				// Order list actions.
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_orders_custom_statuses_add_to_order_list_actions', 'no' ) ) ) {
					add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_custom_status_actions_buttons' ), PHP_INT_MAX, 2 );
					add_action( 'admin_head', array( $this, 'add_custom_status_actions_buttons_css' ) );
				}

				// Order preview actions.
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_orders_custom_statuses_add_to_order_preview_actions', 'no' ) ) ) {
					add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'add_custom_order_statuses_order_preview_actions' ), PHP_INT_MAX, 2 );
				}

				// "Processing" and "Complete" action buttons.
				if ( 'hide' != apply_filters( 'aiow_option', 'hide', aiow_option( 'aiow_orders_custom_statuses_processing_and_completed_actions', 'hide' ) ) ) {
					add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_custom_status_to_processing_and_completed_actions' ), PHP_INT_MAX, 2 );
				}

				// Is order editable.
				if ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_orders_custom_statuses_is_order_editable', 'no' ) ) ) {
					add_filter( 'wc_order_is_editable', array( $this, 'add_custom_order_statuses_to_order_editable' ), PHP_INT_MAX, 2 );
				}

				// Default order status forcefully.
				if ( 'yes' === aiow_option( 'aiow_orders_custom_statuses_default_status_forcefully', 'no' ) ) {
					add_action( 'woocommerce_thankyou', array( $this, 'set_default_order_status_forcefully' ), PHP_INT_MAX, 1 );
				}
			}
		}

		/**
		 * Get custom order statuses.
		 *
		 * @param bool $cut_prefix Cut prefix.
		 * @return array
		 */
		function get_custom_order_statuses( $cut_prefix = false ) {
			$orders_custom_statuses = aiow_option( 'aiow_orders_custom_statuses_array', '' );
			if ( empty( $orders_custom_statuses ) ) {
				return array();
			} else {
				if ( $cut_prefix ) {
					$orders_custom_statuses_no_prefix = array();
					foreach ( $orders_custom_statuses as $status => $status_name ) {
						$orders_custom_statuses_no_prefix[ substr( $status, 3 ) ] = $status_name;
					}
					return $orders_custom_statuses_no_prefix;
				} else {
					return $orders_custom_statuses;
				}
			}
		}

		/**
		 * Get custom order statuses actions.
		 *
		 * @param object $_order Order data.
		 * @return array
		 */
		function get_custom_order_statuses_actions( $_order ) {
			$status_actions        = array();
			$custom_order_statuses = $this->get_custom_order_statuses( true );
			if ( ! empty( $custom_order_statuses ) && is_array( $custom_order_statuses ) ) {
				foreach ( $custom_order_statuses as $custom_order_status => $label ) {
					if ( ! $_order->has_status( array( $custom_order_status ) ) ) { // if order status is not $custom_order_status
						$status_actions[ $custom_order_status ] = $label;
					}
				}
			}
			return $status_actions;
		}

		/**
		 * Get custom order statuses action url.
		 *
		 * @param string $status Order status.
		 * @param int    $order_id Order ID.
		 * @return string
		 */
		function get_custom_order_statuses_action_url( $status, $order_id ) {
			return wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $status . '&order_id=' . $order_id ), 'woocommerce-mark-order-status' );
		}

		/**
		 * Add custom order statuses order preview actions.
		 *
		 * @param array  $actions Actions.
		 * @param object $_order Order data.
		 * @return array
		 */
		function add_custom_order_statuses_order_preview_actions( $actions, $_order ) {
			$status_actions        = array();
			$_status_actions       = $this->get_custom_order_statuses_actions( $_order );
			if ( ! empty( $_status_actions ) ) {
				$order_id = aiow_get_order_id( $_order );
				foreach ( $_status_actions as $custom_order_status => $label ) {
					$status_actions[ $custom_order_status ] = array(
						'url'       => $this->get_custom_order_statuses_action_url( $custom_order_status, $order_id ),
						'name'      => $label,
						'title'     => sprintf( __( 'Change order status to %s', 'all-in-one-wc' ), $custom_order_status ),
						'action'    => $custom_order_status,
					);
				}
			}
			if ( $status_actions ) {
				if ( ! empty( $actions['status']['actions'] ) && is_array( $actions['status']['actions'] ) ) {
					$actions['status']['actions'] = array_merge( $actions['status']['actions'], $status_actions );
				} else {
					$actions['status'] = array(
						'group'   => __( 'Change status: ', 'woocommerce' ),
						'actions' => $status_actions,
					);
				}
			}
			return $actions;
		}

		/**
		 * Add custom order statuses to order editable.
		 *
		 * @param bool   $is_editable Is editable.
		 * @param object $_order Order data.
		 * @return array
		 */
		function add_custom_order_statuses_to_order_editable( $is_editable, $_order ) {
			return ( in_array( $_order->get_status(), array_keys( $this->get_custom_order_statuses( true ) ) ) ? true : $is_editable );
		}

		/**
		 * Add custom status to processing and completed actions.
		 *
		 * @param string $actions Actions.
		 * @param object $_order Order data.
		 * @return array
		 */
		function add_custom_status_to_processing_and_completed_actions( $actions, $_order ) {
			$custom_order_statuses = $this->get_custom_order_statuses();
			if ( ! empty( $custom_order_statuses ) && is_array( $custom_order_statuses ) ) {
				$custom_order_statuses_without_wc_prefix = array();
				foreach ( $custom_order_statuses as $slug => $label ) {
					$custom_order_statuses_without_wc_prefix[] = substr( $slug, 3 );
				}
				global $post;
				$default_actions = array();
				$show = apply_filters( 'aiow_option', 'hide', aiow_option( 'aiow_orders_custom_statuses_processing_and_completed_actions', 'hide' ) );
				if (
					( 'show_both' === $show || 'show_processing' === $show ) &&
					$_order->has_status( array_merge( array( 'pending', 'on-hold' ), $custom_order_statuses_without_wc_prefix ) )
				) {
					$default_actions['processing'] = array(
						'url'       => $this->get_custom_order_statuses_action_url( 'processing', $post->ID ),
						'name'      => __( 'Processing', 'woocommerce' ),
						'action'    => "processing",
					);
				}
				if (
					( 'show_both' === $show || 'show_complete' === $show ) &&
					$_order->has_status( array_merge( array( 'pending', 'on-hold', 'processing' ), $custom_order_statuses_without_wc_prefix ) )
				) {
					$default_actions['complete'] = array(
						'url'       => $this->get_custom_order_statuses_action_url( 'completed', $post->ID ),
						'name'      => __( 'Complete', 'woocommerce' ),
						'action'    => "complete",
					);
				}
				$actions = array_merge( $default_actions, $actions );
			}
			return $actions;
		}

		/**
		 * Add custom status actions buttons.
		 *
		 * @param array  $actions Actions.
		 * @param object $_order  Order data.
		 * @return array
		 */
		function add_custom_status_actions_buttons( $actions, $_order ) {
			$_status_actions = $this->get_custom_order_statuses_actions( $_order );
			if ( ! empty( $_status_actions ) ) {
				$order_id = aiow_get_order_id( $_order );
				foreach ( $_status_actions as $custom_order_status => $label ) {
					$actions[ $custom_order_status ] = array(
						'url'       => $this->get_custom_order_statuses_action_url( $custom_order_status, $order_id ),
						'name'      => $label,
						'action'    => "view " . $custom_order_status, // setting "view" for proper button CSS
					);
				}
			}
			return $actions;
		}

		/**
		 * Get status icon data.
		 *
		 * @param string $status_slug_without_wc_prefix Prefix.
		 * @return array
		 */
		function get_status_icon_data( $status_slug_without_wc_prefix ) {
			$return = array(
				'content'    => 'e011',
				'color'      => '#999999',
				'text_color' => '#000000',
			);
			if ( '' != ( $icon_data = aiow_option( 'aiow_orders_custom_status_icon_data_' . $status_slug_without_wc_prefix, '' ) ) ) {
				$return['content'] = $icon_data['content'];
				$return['color']   = $icon_data['color'];
				if ( isset( $icon_data['text_color'] ) ) {
					$return['text_color'] = $icon_data['text_color'];
				}
			}
			return $return;
		}

		/**
		 * Add custom status actions buttons css.
		 */
		function add_custom_status_actions_buttons_css() {
			$custom_order_statuses = $this->get_custom_order_statuses( true );
			foreach ( $custom_order_statuses as $slug => $label ) {
				$icon_data   = $this->get_status_icon_data( $slug );
				$color_style = ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_orders_custom_statuses_add_to_order_list_actions_colored', 'no' ) ) ) ?
					' color: ' . $icon_data['color'] . ' !important;' : '';
				echo '<style>.view.' . $slug . '::after { font-family: WooCommerce !important;' . $color_style .
					' content: "\\' . $icon_data['content'] . '" !important; }</style>';
			}
		}

		/**
		 * Add custom order statuses to reports.
		 *
		 * @param array $order_statuses Order status.
		 * @return array
		 */
		function add_custom_order_statuses_to_reports( $order_statuses ) {
			if ( is_array( $order_statuses ) && in_array( 'completed', $order_statuses ) ) {
				return array_merge( $order_statuses, array_keys( $this->get_custom_order_statuses( true ) ) );
			} else {
				return $order_statuses;
			}
		}

		/**
		 * Set default order status.
		 *
		 * @param array $status Order status.
		 * @return array
		 */
		function set_default_order_status( $status ) {
			return ( 'aiow_no_changes' != ( $default_status = aiow_option( 'aiow_orders_custom_statuses_default_status', 'pending' ) ) ? $default_status : $status );
		}

		/**
		 * Set default order status forcefully.
		 *
		 * @param int $order_id Order ID.
		 * @return null
		 */
		function set_default_order_status_forcefully( $order_id ){
			if( ! $order_id ) return;
			$order = wc_get_order( $order_id );
			$order->update_status( aiow_option( 'aiow_orders_custom_statuses_default_status' ) );
		}

		/**
		 * Register custom post statuses.
		 */
		function register_custom_post_statuses() {
			$custom_statuses = $this->get_custom_order_statuses( $this->cut_prefix() );
			foreach ( $custom_statuses as $slug => $label ) {
				register_post_status( $slug, array(
					'label'                     => $label,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ),
				) );
			}
		}

		/**
		 * Remove prefix.
		 *
		 * @return mixed
		 */
		function cut_prefix() {
			return filter_var( aiow_option( 'aiow_orders_custom_statuses_remove_prefix', 'no' ), FILTER_VALIDATE_BOOLEAN );
		}

		/**
		 * Add custom statuses to filter.
		 *
		 * @param array $order_statuses Order status.
		 * @return array
		 */
		function add_custom_statuses_to_filter( $order_statuses ) {
			if (
				function_exists('get_current_screen') &&
				! empty( $screen = get_current_screen() ) &&
				'edit-shop_order' === $screen->id
			) {
				$custom_order_statuses = $this->get_custom_order_statuses( $this->cut_prefix() );
			} else {
				$custom_order_statuses = $this->get_custom_order_statuses();
			}
			return array_merge( ( '' == $order_statuses ? array() : $order_statuses ), $custom_order_statuses );
		}

		/**
		 * Order status css.
		 */
		function hook_statuses_column_css() {
			$output   = '';
			$statuses = $this->get_custom_order_statuses( true );
			foreach( $statuses as $status => $status_name ) {
				$icon_data = $this->get_status_icon_data( $status );
				$output .= 'mark.order-status.status-' . $status . ' { color: ' . $icon_data['text_color'] . '; background-color: ' . $icon_data['color'] . '; }';
			}
			if ( '' != $output ) {
				echo '<style>' . $output . '</style>';
			}
		}

		/**
		 * Status icon CSS.
		 */
		function hook_statuses_icons_css() {
			$output   = '';
			$statuses = $this->get_custom_order_statuses( true );
			foreach( $statuses as $status => $status_name ) {
				$icon_data = $this->get_status_icon_data( $status );
				$output .= 'mark.' . $status . '::after { content: "\\' . $icon_data['content'] . '"; color: ' . $icon_data['color'] . '; }';
				$output .= 'mark.' . $status . ':after {font-family:WooCommerce;speak:none;font-weight:400;font-variant:normal;text-transform:none;' .
					'line-height:1;-webkit-font-smoothing:antialiased;margin:0;text-indent:0;position:absolute;top:0;left:0;width:100%;height:100%;text-align:center}';
			}
			if ( '' != $output ) {
				echo '<style>' . $output . '</style>';
			}
		}

		/**
		 * Add extra bulk action options to mark orders as complete or processing.
		 */
		function bulk_admin_footer() {
			global $post_type;
			if ( 'shop_order' == $post_type ) {
				?><script type="text/javascript"><?php
				foreach( aiow_get_order_statuses() as $key => $order_status ) {
					if ( in_array( $key, array( 'processing', 'on-hold', 'completed', ) ) ) continue;
					?>jQuery(function() {
						jQuery('<option>').val('mark_<?php echo $key; ?>').text('<?php echo __( 'Mark', 'all-in-one-wc' ) . ' ' .
							$order_status; ?>').appendTo('select[name="action"]');
						jQuery('<option>').val('mark_<?php echo $key; ?>').text('<?php echo __( 'Mark', 'all-in-one-wc' ) . ' ' .
							$order_status; ?>').appendTo('select[name="action2"]');
					});<?php
				}
				?></script><?php
			}
		}
	}
}
