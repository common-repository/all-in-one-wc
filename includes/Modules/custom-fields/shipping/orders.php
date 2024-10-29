<?php
/**
 * Shipping - Settings - Orders
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$bulk_regenerate_download_permissions_all_orders_cron_desc = '';
if ( $this->is_enabled() && 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_order_bulk_regenerate_download_permissions_enabled', 'no' ) ) ) {
	$bulk_regenerate_download_permissions_all_orders_cron_desc = aiow_crons_get_next_event_time_message( 'aiow_bulk_regenerate_download_permissions_all_orders_cron_time' );
}

$payment_gateways_options = array();
if ( function_exists( 'WC' ) && is_callable( array( WC()->payment_gateways, 'payment_gateways' ) ) ) {
	foreach ( WC()->payment_gateways->payment_gateways() as $payment_gateway_key => $payment_gateway_data ) {
		$payment_gateways_options[ $payment_gateway_key ] = $payment_gateway_data->title;
	}
}

$settings = array(
	array(
		'title'    => __( 'Admin Order Currency', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_admin_currency_options',
	),
	array(
		'title'    => __( 'Admin Order Currency', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'When enabled this will add "Orders" metabox to each order\'s edit page.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_currency',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Admin Order Currency Method', 'all-in-one-wc' ),
		'desc_tip' => __( 'Choose if you want changed order currency to be saved directly to DB, or if you want to use filter. When using <em>filter</em> method, changes will be active only when "Admin Order Currency" section is enabled. When using <em>directly to DB</em> method, changes will be permanent, that is even if plugin is removed.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_currency_method',
		'default'  => 'filter',
		'type'     => 'select',
		'options'  => array(
			'filter' => __( 'Filter', 'all-in-one-wc' ),
			'db'     => __( 'Directly to DB', 'all-in-one-wc' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_admin_currency_options',
	),
	array(
		'title'    => __( 'Admin Order Navigation', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_orders_navigation_options',
	),
	array(
		'title'    => __( 'Admin Order Navigation', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'When enabled, this will add "Order Navigation" metabox to each order\'s admin edit page.', 'all-in-one-wc' ) . ' ' .
			__( 'Metabox will contain "Previous order" and "Next order" links.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_navigation_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_orders_navigation_options',
	),
	array(
		'title'    => __( 'Editable Orders', 'all-in-one-wc' ),
		'desc'     => __( 'This section allows you to set which order statuses are editable.', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_orders_editable_status_options',
	),
	array(
		'title'    => __( 'Editable Orders Statuses', 'all-in-one-wc' ),
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_editable_status_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'id'       => 'aiow_orders_editable_status',
		'default'  => array( 'pending', 'on-hold', 'auto-draft' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array_merge( aiow_get_order_statuses(), array( 'auto-draft' => __( 'Auto-draft', 'all-in-one-wc' ) ) ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_orders_editable_status_options',
	),
	array(
		'title'    => __( 'Orders Auto-Complete', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable orders auto-complete function.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_auto_complete_options',
	),
	array(
		'title'    => __( 'Auto-complete all WooCommerce orders', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'E.g. if you sell digital products then you are not shipping anything and you may want auto-complete all your orders.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_auto_complete_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Payment methods', 'all-in-one-wc' ) . '<br>' . apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Fill this, if you want orders to be auto-completed for selected payment methods only. Leave blank to auto-complete all orders.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_auto_complete_payment_methods',
		'default'  => array(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $payment_gateways_options,
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_auto_complete_options',
	),
	array(
		'title'    => __( 'Country by IP', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_orders_country_by_ip_options',
	),
	array(
		'title'    => __( 'Add Country by IP Meta Box', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'desc_tip' => __( 'When enabled this will add "Country by IP" metabox to each order\'s edit page.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_country_by_ip_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_orders_country_by_ip_options',
	),
	array(
		'title'    => __( 'Bulk Regenerate Download Permissions for Orders', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_options',
	),
	array(
		'title'    => __( 'Bulk Regenerate Download Permissions', 'all-in-one-wc' ),
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Bulk Actions', 'all-in-one-wc' ),
		'desc_tip' => __( 'When enabled this will add "Regenerate download permissions" action to "Bulk Actions" select box on admin orders page.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'All Orders - Now', 'all-in-one-wc' ),
		'desc_tip' => __( 'Check this box and press "Save changes" button to start regeneration. Please note that both module and current section must be enabled before that.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'desc'     => __( 'Regenerate now', 'all-in-one-wc' ),
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_all_orders',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'All Orders - Periodically', 'all-in-one-wc' ),
		'desc'     => $bulk_regenerate_download_permissions_all_orders_cron_desc . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_all_orders_cron',
		'default'  => 'disabled',
		'type'     => 'select',
		'options'  => array_merge( array( 'disabled' => __( 'Disabled', 'all-in-one-wc' ) ),
			aiow_crons_get_all_intervals( __( 'Regenerate', 'all-in-one-wc' ), array( 'minutely' ) ) ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_bulk_regenerate_download_permissions_options',
	),
);
return $settings;
