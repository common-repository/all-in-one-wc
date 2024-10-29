<?php
/**
 *  PDF Invoicing - Templates Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array();
$invoice_types = ( 'yes' === aiow_option( 'aiow_invoicing_hide_disabled_docs_settings', 'no' ) ) ? aiow_get_enabled_invoice_types() : aiow_get_invoice_types();
foreach ( $invoice_types as $invoice_type ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'type'     => 'title',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_templates_options',
		),
		array(
			'title'    => __( 'HTML Template', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_template',
			'default'  => $this->get_default_template( $invoice_type['id'] ),
			'type'     => 'textarea',
			'css'      => 'width:100%;height:500px;',
		),
		array(
			'title'    => __( 'Save all templates', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_template_save_all',
			'type'     => 'aiow_save_settings_button',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_templates_options',
		),
	) );
}
return $settings;
