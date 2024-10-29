<?php
/**
 * Shipping - Settings - Admin Orders List
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Custom Columns', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you add custom columns to WooCommerce orders list.', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_list_custom_columns_options',
	),
	array(
		'title'    => __( 'Custom Columns', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_admin_list_custom_columns_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Billing Country', 'all-in-one-wc' ),
		'desc'     => __( 'Add column and filtering', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_list_custom_columns_country',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Currency Code', 'all-in-one-wc' ),
		'desc'     => __( 'Add column and filtering', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_list_custom_columns_currency',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Custom Columns Total Number', 'all-in-one-wc' ),
		'id'       => 'aiow_orders_list_custom_columns_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'aiow_message', '', 'readonly' ) ) ? apply_filters( 'aiow_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '0', )
		),
	),
);
$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_orders_list_custom_columns_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Custom Column', 'all-in-one-wc' ) . ' #' . $i,
			'desc'     => __( 'Enabled', 'all-in-one-wc' ),
			'desc_tip' => __( 'Key:', 'all-in-one-wc' ) . ' <code>' . 'aiow_orders_custom_column_' . $i . '</code>',
			'id'       => 'aiow_orders_list_custom_columns_enabled_' . $i,
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Label', 'all-in-one-wc' ),
			'id'       => 'aiow_orders_list_custom_columns_label_' . $i,
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
		array(
			'desc'     => __( 'Value', 'all-in-one-wc' ),
			'desc_tip' => __( 'You can use shortcodes and/or HTML here.', 'all-in-one-wc' ),
			'id'       => 'aiow_orders_list_custom_columns_value_' . $i,
			'default'  => '',
			'type'     => 'custom_textarea',
			'css'      => 'width:100%;',
		),
		array(
			'desc'     => __( 'Sortable , Select "By meta (as text)" for date sorting', 'all-in-one-wc' ),
			'id'       => 'aiow_orders_list_custom_columns_sortable_' . $i,
			'default'  => 'no',
			'type'     => 'select',
			'options'  => array(
				'no'             => __( 'No', 'all-in-one-wc' ),
				'meta_value'     => __( 'By meta (as text)', 'all-in-one-wc' ),
				'meta_value_num' => __( 'By meta (as numbers)', 'all-in-one-wc' ),
			),
		),
		array(
			'desc'     => sprintf( __( 'Key (if sortable) %s Add "_" (underscore) before key if the key is from "Checkout Custom Fields module"') , '</br>' , 'all-in-one-wc' ),
			'id'       => 'aiow_orders_list_custom_columns_sortable_key_' . $i,
			'default'  => '',
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_orders_list_custom_columns_options',
	),
	array(
		'title'    => __( 'Multiple Status', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_admin_list_multiple_status_options',
	),
	array(
		'title'    => __( 'Multiple Status', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_admin_list_multiple_status_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Multiple Status Filtering', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_filter',
		'default'  => 'no',
		'type'     => 'select',
		'options'  => array(
			'no'              => __( 'Do not add', 'all-in-one-wc' ),
			'multiple_select' => __( 'Add as multiple select', 'all-in-one-wc' ),
			'checkboxes'      => __( 'Add as checkboxes', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Hide Default Statuses Menu', 'all-in-one-wc' ),
		'desc'     => __( 'Hide', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_hide_default_statuses_menu',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add "Not Completed" Status Link to Default Statuses Menu', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_not_completed_link',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Add Presets to Admin Menu', 'all-in-one-wc' ),
		'desc'     => '<strong>' .  __( 'Add presets', 'all-in-one-wc' ) . '</strong>',
		'desc_tip' => __( 'To add presets, "Multiple Status Filtering" option must be enabled (as multiple select or as checkboxes).', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_admin_menu',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Add order counter', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_admin_menu_counter',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Remove original "Orders" menu', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_admin_menu_remove_original',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Total Presets', 'all-in-one-wc' ),
		'id'       => 'aiow_order_admin_list_multiple_status_presets_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
) );
$total_number = apply_filters( 'aiow_option', 1, aiow_option( 'aiow_order_admin_list_multiple_status_presets_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'desc'     => __( 'Title', 'all-in-one-wc' ),
			'desc_tip' => __( 'Must be not empty.', 'all-in-one-wc' ),
			'id'       => "aiow_order_admin_list_multiple_status_presets_titles[$i]",
			'default'  => __( 'Preset', 'all-in-one-wc' ) . ' #' . $i,
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Statuses', 'all-in-one-wc' ),
			'desc_tip' => __( 'Must be not empty.', 'all-in-one-wc' ),
			'id'       => "aiow_order_admin_list_multiple_status_presets_statuses[$i]",
			'default'  => array(),
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => aiow_get_order_statuses( false ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_admin_list_multiple_status_options',
	),
	array(
		'title'    => __( 'Columns Order', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_admin_list_columns_order_options',
	),
	array(
		'title'    => __( 'Columns Order', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_admin_list_columns_order_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'aiow_order_admin_list_columns_order',
		'desc_tip' => __( 'Default columns order', 'all-in-one-wc' ) . ':<br>' . str_replace( PHP_EOL, '<br>', $this->get_orders_default_columns_in_order() ),
		'default'  => $this->get_orders_default_columns_in_order(),
		'type'     => 'textarea',
		'css'      => 'height:300px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_admin_list_columns_order_options',
	),
) );
return $settings;
