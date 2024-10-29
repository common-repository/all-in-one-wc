<?php
/**
 * Shipping - Settings - Order Numbers
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

return array(
	array(
		'title'    => __( 'Order Numbers', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable sequential order numbering, set custom number prefix, suffix and width.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_numbers_options',
	),
	array(
		'title'    => __( 'Number Generation', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_sequential_enabled',
		'default'  => 'yes',
		'type'     => 'select',
		'options'  => array(
			'yes'        => __( 'Sequential', 'all-in-one-wc' ),
			'no'         => __( 'Order ID', 'all-in-one-wc' ),
			'hash_crc32' => __( 'Pseudorandom - Hash (max 10 digits)', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Sequential: Next Order Number', 'all-in-one-wc' ),
		'desc'     => '<br>' . __( 'Next new order will be given this number.', 'all-in-one-wc' ) . ' ' . __( 'Use Renumerate Orders tool for existing orders.', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will be ignored if sequential order numbering is disabled.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_counter',
		'default'  => 1,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Sequential: Reset Counter', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will be ignored if sequential order numbering is disabled.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_counter_reset_enabled',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'      => __( 'Disabled', 'all-in-one-wc' ),
			'daily'   => __( 'Daily', 'all-in-one-wc' ),
			'monthly' => __( 'Monthly', 'all-in-one-wc' ),
			'yearly'  => __( 'Yearly', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Order Number Custom Prefix', 'all-in-one-wc' ),
		'desc_tip' => __( 'Prefix before order number (optional). This will change the prefixes for all existing orders.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_prefix',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Order Number Date Prefix', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Date prefix before order number (optional). This will change the prefixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_date_prefix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Width', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Minimum width of number without prefix (zeros will be added to the left side). This will change the minimum width of order number for all existing orders. E.g. set to 5 to have order number displayed as 00001 instead of 1. Leave zero to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_min_width',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Custom Suffix', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Suffix after order number (optional). This will change the suffixes for all existing orders.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_suffix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Order Number Date Suffix', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Date suffix after order number (optional). This will change the suffixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_date_suffix',
		'default'  => '',
		'type'     => 'text',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Use MySQL Transaction', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This should be enabled if you have a lot of simultaneous orders in your shop - to prevent duplicate order numbers (sequential).', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_use_mysql_transaction_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Order Tracking by Custom Number', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_order_tracking_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Order Admin Search by Custom Number', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_search_by_custom_number_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Editable Order Number Meta Box', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_order_number_editable_order_number_meta_box_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Minimal Order ID', 'all-in-one-wc' ),
		'desc_tip' => __( 'If you wish to disable order numbering for some (older) orders, you can set order ID to start here.', 'all-in-one-wc' ) . ' ' .
			__( 'Set to zero to enable numbering for all orders.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_numbers_min_order_id',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_numbers_options',
	),
	array(
		'title'    => __( 'Compatibility', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_numbers_compatibility',
	),
	array(
		'title'             => __( 'WPNotif', 'all-in-one-wc' ),
		'desc'              => empty( $message = apply_filters( 'aiow_message', '', 'desc' ) ) ? __( 'Enable', 'all-in-one-wc' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WPNotif: WordPress SMS & WhatsApp Notifications</a> plugin fixing the <code>{{wc-tracking-link}}</code> variable.', 'all-in-one-wc' ), 'https://wpnotif.unitedover.com/' ),
		'id'                => 'aiow_order_numbers_compatibility_wpnotif',
		'default'           => 'no',
		'type'              => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_numbers_compatibility',
	),
	array(
		'title'    => __( 'Orders Renumerate Tool Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_numbers_renumerate_tool_options',
	),
	array(
		'title'    => __( 'Sort by', 'all-in-one-wc' ),
		'id'       => 'aiow_order_numbers_renumerate_tool_orderby',
		'default'  => 'date',
		'type'     => 'select',
		'options'  => array(
			'ID'       => __( 'ID', 'all-in-one-wc' ),
			'date'     => __( 'Date', 'all-in-one-wc' ),
			'modified' => __( 'Last modified date', 'all-in-one-wc' ),
			'rand'     => __( 'Random', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Sort Ascending or Descending', 'all-in-one-wc' ),
		'id'       => 'aiow_order_numbers_renumerate_tool_order',
		'default'  => 'ASC',
		'type'     => 'select',
		'options'  => array(
			'ASC'  => __( 'Ascending', 'all-in-one-wc' ),
			'DESC' => __( 'Descending', 'all-in-one-wc' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_numbers_renumerate_tool_options',
	),
);
