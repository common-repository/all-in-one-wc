<?php
/**
 * Shipping - Settings - Order Custom Statuses
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

return array(
	array(
		'title'    => __( 'Custom Statuses', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_orders_custom_statuses_options',
	),
	array(
		'title'    => __( 'Default Order Status', 'all-in-one-wc' ),
		'desc'     => __( 'Enable the module to add custom statuses to the list.', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can change the default order status here. However payment gateways can change this status immediately on order creation. E.g. BACS gateway will change status to On-hold.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_custom_statuses_default_status',
		'default'  => 'aiow_no_changes',
		'type'     => 'select',
		'options'  => array_merge( array( 'aiow_no_changes' => __( 'No changes', 'all-in-one-wc' ) ), aiow_get_order_statuses() ),
	),
	array(
		'title'    => __( 'Set Default Order Status Forcefully', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can set the default order status forcefully from here. Forcing the status can result in unpredictable consequences, enable the checkbox here.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_custom_statuses_default_status_forcefully',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add All Statuses to Admin Order Bulk Actions', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'desc_tip' => __( 'If you wish to add custom statuses to admin Orders page bulk actions, enable the checkbox here.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_custom_statuses_add_to_bulk_actions',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add Custom Statuses to Admin Reports', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'desc_tip' => __( 'If you wish to add custom statuses to admin reports, enable the checkbox here.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_custom_statuses_add_to_reports',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Make Custom Status Orders Editable', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'By default orders with custom statuses are not editable (same like with standard WooCommerce Completed status). If you wish to make custom status orders editable, enable the checkbox here.', 'all-in-one-wc' ) . ' ' .
			apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_is_order_editable',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Remove Status Prefix', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Removes the <code>wc-</code> prefix from custom statuses.', 'all-in-one-wc' ) . ' ' . __( 'Enable it if you can\'t see the orders or the statuses.', 'all-in-one-wc' ) . ' ' .
		              apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_remove_prefix',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( '"Processing" and "Complete" Action Buttons', 'all-in-one-wc' ),
		'desc_tip' => __( 'By default, when order has custom status, "Processing" and "Complete" action buttons are hidden. You can enable it here. Possible values are: Show both; Show "Processing" only; Show "Complete" only; Hide (default).', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_custom_statuses_processing_and_completed_actions',
		'default'  => 'hide',
		'type'     => 'select',
		'options'  => array(
			'show_both'       => __( 'Show both', 'all-in-one-wc' ),
			'show_processing' => __( 'Show "Processing" only', 'all-in-one-wc' ),
			'show_complete'   => __( 'Show "Complete" only', 'all-in-one-wc' ),
			'hide'            => __( 'Hide', 'all-in-one-wc' ),
		),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Add Custom Statuses to Admin Order List Action Buttons', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'desc_tip' => __( 'If you wish to add custom statuses buttons to the admin Orders page action buttons (Actions column), enable the checkbox here.', 'all-in-one-wc' ) . ' ' .
			apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_add_to_order_list_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'desc'     => __( 'Enable Colors', 'all-in-one-wc' ),
		'desc_tip' => __( 'Choose if you want the buttons to have colors.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_add_to_order_list_actions_colored',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Enable Colors in Status Column', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want the statuses in Status column to have colors.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_column_colored',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Add Custom Statuses Buttons to Admin Order Preview Actions', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'desc_tip' => __( 'If you wish to add custom statuses buttons to the admin orders preview page, enable the checkbox here.', 'all-in-one-wc' ) . ' ' .
			apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_orders_custom_statuses_add_to_order_preview_actions',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_orders_custom_statuses_options',
	),
);
