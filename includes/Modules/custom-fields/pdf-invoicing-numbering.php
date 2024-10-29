<?php
/**
 * PDF Invoicing Numbering - Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings      = array();
$invoice_types = ( 'yes' === aiow_option( 'aiow_invoicing_hide_disabled_docs_settings', 'no' ) ? aiow_get_enabled_invoice_types() : aiow_get_invoice_types() );
foreach ( $invoice_types as $invoice_type ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'type'     => 'title',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_options',
		),
		array(
			'title'    => __( 'Sequential', 'all-in-one-wc' ),
			'desc'     => '<strong>' . __( 'Enable', 'all-in-one-wc' ) . '</strong>',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_sequential_enabled',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Counter', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_counter',
			'default'  => 1,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Counter Width', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_counter_width',
			'default'  => 0,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Prefix', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_prefix',
			'default'  => '',
			'type'     => 'text',
		),
		array(
			'title'    => __( 'Suffix', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_suffix',
			'default'  => '',
			'type'     => 'text',
		),
		array(
			'title'    => __( 'Template', 'all-in-one-wc' ),
			'desc'     => '<br>' . aiow_message_replaced_values( array( '%prefix%', '%counter%', '%suffix%' ) ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_template',
			'default'  => '%prefix%%counter%%suffix%',
			'type'     => 'text',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_numbering_options',
		),
	) );
}
return $settings;
