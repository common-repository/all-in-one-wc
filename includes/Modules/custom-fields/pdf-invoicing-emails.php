<?php
/**
 * PDF Invoicing Email Options - Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$available_gateways = WC()->payment_gateways->payment_gateways();
foreach ( $available_gateways as $key => $gateway ) {
	$available_gateways_options_array[ $key ] = $gateway->title;
}
$available_emails = array();
$wc_emails = WC()->mailer()->get_emails();
foreach ( $wc_emails as $wc_email ) {
	if ( isset( $wc_email->id ) && isset( $wc_email->title ) ) {
		$available_emails[ $wc_email->id ] = $wc_email->title;
	}
}
$settings = array();
$invoice_types = ( 'yes' === aiow_option( 'aiow_invoicing_hide_disabled_docs_settings', 'no' ) ) ? aiow_get_enabled_invoice_types() : aiow_get_invoice_types();
foreach ( $invoice_types as $invoice_type ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_emails_options',
		),
		array(
			'title'    => __( 'Attach PDF to emails', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_attach_to_emails',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'default'  => '',
			'options'  => $available_emails,
			'custom_attributes' => array( 'data-placeholder' => __( 'Select some emails', 'all-in-one-wc' ) ),
		),
		array(
			'title'    => __( 'Payment gateways to include', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_payment_gateways',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'css'      => 'width: 450px;',
			'default'  => '',
			'options'  => $available_gateways_options_array,
			'custom_attributes' => array( 'data-placeholder' => __( 'Select some gateways. Leave blank to include all.', 'all-in-one-wc' ) ),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_emails_options',
		),
	) );
}
return $settings;
