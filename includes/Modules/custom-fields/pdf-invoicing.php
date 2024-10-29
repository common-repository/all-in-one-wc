<?php
/**
 * PDF Invoicing - General Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Documents Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_pdf_invoicing_options',
	),
);
// Hooks Array
$status_change_hooks = array();
$order_statuses      = aiow_get_order_statuses();
foreach ( $order_statuses as $status => $desc ) {
	$status_change_hooks[ 'woocommerce_order_status_' . $status ] = sprintf( __( 'Create on Order Status %s', 'all-in-one-wc' ), $desc );
}
$create_on_array = array_merge(
	array(
		'woocommerce_new_order'                             => __( 'Create on New Order', 'all-in-one-wc' ),
	),
	$status_change_hooks,
	array(
		'woocommerce_order_partially_refunded_notification' => __( 'Create on Order Partially Refunded', 'all-in-one-wc' ),
		'manual'                                            => __( 'Manually', 'all-in-one-wc' ),
	)
);
// Settings
$invoice_types = aiow_get_invoice_types();
foreach ( $invoice_types as $k => $invoice_type ) {
	if ( 'custom_doc' === $invoice_type['id'] ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => __( 'Number of Custom Documents', 'all-in-one-wc' ),
				'desc_tip' => __( 'Save changes after setting this number.', 'all-in-one-wc' ),
				'id'       => 'aiow_invoicing_custom_doc_total_number',
				'default'  => 1,
				'type'     => 'aiow_custom_number',
				'custom_attributes' => array( 'min' => '1', 'max' => '100' ),
			),
		) );
	}
	$create_on_value = aiow_get_invoice_create_on( $invoice_type['id'] );
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_create_on',
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => $create_on_array,
			'desc'     => ( 0 === $k ) ? '' : apply_filters( 'aiow_message', '', 'desc' ),
			'custom_attributes' => ( 0 === $k ) ? '' : apply_filters( 'aiow_message', '', 'disabled' ),
		),
		array(
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_skip_zero_total',
			'default'  => 'no',
			'type'     => 'checkbox',
			'desc'     => __( 'Do not create if order total equals zero', 'all-in-one-wc' ),
			'custom_attributes' => ( 0 === $k ) ? '' : apply_filters( 'aiow_message', '', 'disabled' ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_pdf_invoicing_options',
	),
) );
return $settings;
