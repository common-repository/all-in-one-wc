<?php
/**
 * Global function core file.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

require_once plugin_dir_path( __FILE__ ) . '/module-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/general-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/products-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/admin-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/invoicing-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/orders-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/users-functions.php';
require_once plugin_dir_path( __FILE__ ) . '/price-currency.php';
require_once plugin_dir_path( __FILE__ ) . '/country.php';
require_once plugin_dir_path( __FILE__ ) . '/crons.php';
require_once plugin_dir_path( __FILE__ ) . '/shipping.php';

if ( ! function_exists( 'aiow_option' ) ) {

	/**
	 * Get plugin option data.
	 *
	 * @param string $option_name Option Name.
	 * @param bool   $default Default null.
	 * @return mixed
	 */
	function aiow_option( $option_name, $default = null ) {
		if ( ! isset( AIOW()->options[ $option_name ] ) ) {
			AIOW()->options[ $option_name ] = get_option( $option_name, $default );
		}
		return apply_filters( $option_name, AIOW()->options[ $option_name ] );
	}
}


if ( ! function_exists( 'aiow_admin_setting_tab' ) ) {

	/**
	 * Admin setting tab.
	 *
	 * @return array
	 */
	function aiow_admin_setting_tab() {
		/**
		 * WooCommerce setting tab.
		 */
		return apply_filters( 'aiow_setting_tab',
			array(
				'dashboard' => array(
					'label'          => __( 'Dashboard', 'all-in-one-wc' ),
					'desc'           => __( 'This dashboard lets you enable/disable any module. Each checkbox comes with short module\'s description.', 'all-in-one-wc' ),
					'all_cat_ids'    => array(
						'alphabetically',
						'by_category',
						'active',
						//'manager',
					),
				),
				'labels' => array(
					'label'          => __( 'Button & Price Labels', 'all-in-one-wc' ),
					'desc'           => __( 'Add to Cart Labels, Call for Price, Custom Price Labels and more.', 'all-in-one-wc' ),
					'all_cat_ids'    => array(
						'price_labels',
						'call_for_price',
						'free_price',
						'add_to_cart',
						'more_button_labels',
					),
				),
				'pdf_invoicing' => array(
					'label'          => __( 'PDF Invoicing & Packing Slips', 'all-in-one-wc' ),
					'desc'           => __( 'PDF Documents', 'all-in-one-wc' ),
					'all_cat_ids'    => array(
						'pdf_invoicing',
						'pdf_invoicing_numbering',
						'pdf_invoicing_templates',
						'pdf_invoicing_header',
						'pdf_invoicing_footer',
						'pdf_invoicing_styling',
						'pdf_invoicing_page',
						'pdf_invoicing_emails',
						'pdf_invoicing_display',
						'pdf_invoicing_advanced',
					),
				),
				'shipping_and_orders' => array(
					'label'          => __( 'Shipping & Orders', 'all-in-one-wc' ),
					'desc'           => __( 'Order Custom Statuses, Order Minimum Amount, Order Numbers, Custom Shipping Methods and more.', 'all-in-one-wc' ),
					'all_cat_ids'    => array(
						'shipping',
						'shipping_options',
						'shipping_icons',
						'shipping_description',
						'shipping_time',
						'left_to_free_shipping',
						'shipping_calculator',
						'shipping_by_user_role',
						'shipping_by_products',
						'shipping_by_cities',
						'shipping_by_time',
						'shipping_by_order_amount',
						'shipping_by_order_qty',
						'address_formats',
						'orders',
						'admin_orders_list',
						'order_min_amount',
						'order_numbers',
						'order_custom_statuses',
						'order_quantities',
						'max_products_per_user',
					),
				),
			)
		);
	}

	/**
	 * Module list.
	 *
	 * @return array
	 */
	function aiow_module_lists() {

		/**
		 * Core module list.
		 */
		return apply_filters( 'aiow_modules',
			array(
				'call_for_price' => new AIOW\Modules\Price\Call_For_Price(),
				'free_price' => new AIOW\Modules\Price\Free_Price(),
				'price_labels' => new AIOW\Modules\Price\Price_Labels(),
				'add_to_cart' => new AIOW\Modules\AddToCart\AddToCart(),
				'more_button_labels' => new AIOW\Modules\Button\More_Button_Labels(),
				// PDF.
				'pdf_invoicing' => new AIOW\Modules\Invoice\PDF_Invoice(),
				'pdf_invoicing_advanced' => new AIOW\Modules\Invoice\Sub_Modules\Advanced(),
				'pdf_invoicing_display' => new AIOW\Modules\Invoice\Sub_Modules\Display(),
				'pdf_invoicing_emails' => new AIOW\Modules\Invoice\Sub_Modules\Emails(),
				'pdf_invoicing_footer' => new AIOW\Modules\Invoice\Sub_Modules\Footer(),
				'pdf_invoicing_header' => new AIOW\Modules\Invoice\Sub_Modules\Header(),
				'pdf_invoicing_numbering' => new AIOW\Modules\Invoice\Sub_Modules\Numbering(),
				'pdf_invoicing_page' => new AIOW\Modules\Invoice\Sub_Modules\Page(),
				'pdf_invoicing_styling' => new AIOW\Modules\Invoice\Sub_Modules\Styling(),
				'pdf_invoicing_templates' => new AIOW\Modules\Invoice\Sub_Modules\Templates(),
				// Shipping.
				'address_formats' => new AIOW\Modules\Shipping\Address_Formats(),
				'admin_orders_list' => new AIOW\Modules\Shipping\Orders_List(),
				'shipping' => new AIOW\Modules\Shipping\Shipping(),
				'left_to_free_shipping' => new AIOW\Modules\Shipping\Left_To_Free_Shipping(),
				'max_products_per_user' => new AIOW\Modules\Shipping\Max_Products_Per_User(),
				'order_custom_statuses' => new AIOW\Modules\Shipping\Custom_Order_Statuses(),
				'order_min_amount' => new AIOW\Modules\Shipping\Order_Min_Amount(),
				'order_numbers' => new AIOW\Modules\Shipping\Order_Numbers(),
				'order_quantities' => new AIOW\Modules\Shipping\Order_Quantities(),
				'orders' => new AIOW\Modules\Shipping\Orders(),
				'shipping_calculator' => new AIOW\Modules\Shipping\Calculator(),
				'shipping_description' => new AIOW\Modules\Shipping\Description(),
				'shipping_icons' => new AIOW\Modules\Shipping\Icons(),
				'shipping_by_cities' => new AIOW\Modules\Shipping\Cities(),
				'shipping_by_time' => new AIOW\Modules\Shipping\By_Time(),
				'shipping_by_order_amount' => new AIOW\Modules\Shipping\Order_Amount(),
				'shipping_by_order_qty' => new AIOW\Modules\Shipping\Order_Qty(),
				'shipping_by_products' => new AIOW\Modules\Shipping\Products(),
				'shipping_by_user_role' => new AIOW\Modules\Shipping\User_Role(),
				'shipping_options' => new AIOW\Modules\Shipping\Options(),
				'shipping_time' => new AIOW\Modules\Shipping\Time(),
			)
		);
	}
}

if ( ! function_exists( 'aiow_is_user_role' ) ) {
	/**
	 * Is user role.
	 *
	 * @param array $user_role User roles.
	 * @param int   $user_id User ID.
	 * @return bool
	 */
	function aiow_is_user_role( $user_role, $user_id = 0 ) {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . 'wp-includes/pluggable.php' );
		}
		$_user = ( 0 == $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id ) );
		if ( ! isset( $_user->roles ) || empty( $_user->roles ) ) {
			$_user->roles = array( 'guest' );
		}
		if ( ! is_array( $_user->roles ) ) {
			return false;
		}
		if ( is_array( $user_role ) ) {
			if ( in_array( 'administrator', $user_role ) ) {
				$user_role[] = 'super_admin';
			}
			$_intersect = array_intersect( $user_role, $_user->roles );
			return ( ! empty( $_intersect ) );
		} else {
			if ( 'administrator' == $user_role ) {
				return ( in_array( 'administrator', $_user->roles ) || in_array( 'super_admin', $_user->roles ) );
			} else {
				return ( in_array( $user_role, $_user->roles ) );
			}
		}
	}
}


if ( ! function_exists( 'aiow_get_js_confirmation' ) ) {
	/**
	 * Confirmation dialog box.
	 *
	 * @param string $confirmation_message Confirmation Message.
	 * @return string
	 */
	function aiow_get_js_confirmation( $confirmation_message = '' ) {
		if ( '' === $confirmation_message ) {
			$confirmation_message = __( 'Are you sure?', 'all-in-one-wc' );
		}
		return ' onclick="return confirm(\'' . $confirmation_message . '\')"';
	}
}

if ( ! function_exists( 'aiow_get_table_html' ) ) {
	/**
	 * Get table HTML.
	 *
	 * @param array $data Data.
	 * @param array $args Argument.
	 * @return string
	 */
	function aiow_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		extract( $args );
		$table_class = ( '' == $table_class ) ? '' : ' class="' . $table_class . '"';
		$table_style = ( '' == $table_style ) ? '' : ' style="' . $table_style . '"';
		$row_styles  = ( '' == $row_styles )  ? '' : ' style="' . $row_styles  . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$row_class = 'aiow-row aiow-row' . $row_number;
			$row_class .= $row_number % 2 == 0 ? ' aiow-row-even' : ' aiow-row-odd';
			$html .= '<tr' . $row_styles . ' class="'.$row_class.'">';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $table_heading_type ) || ( 0 === $column_number && 'vertical' === $table_heading_type ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $columns_classes ) && isset( $columns_classes[ $column_number ] ) ) ? ' class="' . $columns_classes[ $column_number ] . '"' : '';
				$column_style = ( ! empty( $columns_styles ) && isset( $columns_styles[ $column_number ] ) ) ? ' style="' . $columns_styles[ $column_number ] . '"' : '';

				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}

if ( ! function_exists( 'aiow_register_shortcodes' ) ) {

	/**
	 * Register shortcode.
	 *
	 * @return array
	 */
	function aiow_register_shortcodes() {
		if ( ! aiow_is_module_enabled( 'general' ) || ( aiow_is_module_enabled( 'general' ) && 'no' === aiow_option( 'aiow_general_shortcodes_disable_shortcodes', 'no' ) ) ) {
			return array(
				'cart' => new AIOW\Shortcodes\Cart(),
				'invoices' => new AIOW\Shortcodes\Invoices(),
				'orders' => new AIOW\Shortcodes\Orders(),
				'order_items' => new AIOW\Shortcodes\Order_Items(),
			);
		}
	}
}
