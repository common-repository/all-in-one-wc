<?php
/**
 * PDF Invoicing Footer - Settings
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
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_options',
		),
		array(
			'title'    => __( 'Enable Footer', 'all-in-one-wc' ),
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_enabled',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Footer Text', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_text',
			'default'  => __( 'Page %page_number% / %total_pages%', 'all-in-one-wc' ),
			'type'     => 'textarea',
			'css'      => 'width:100%;height:165px;',
			'desc'     => __( 'You can use HTML here, as well as any WordPress shortcodes.', 'all-in-one-wc' ) . ' ' .
				aiow_message_replaced_values( array( '%page_number%', '%total_pages%' ) ),
		),
		array(
			'title'    => __( 'Footer Text Color', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_text_color',
			'default'  => '#cccccc',
			'type'     => 'color',
			'css'      => 'width:6em;',
		),
		array(
			'title'    => __( 'Footer Line Color', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_line_color',
			'default'  => '#cccccc',
			'type'     => 'color',
			'css'      => 'width:6em;',
		),
		array(
			'title'    => __( 'Footer Margin', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_footer',
			'default'  => 10, // PDF_MARGIN_FOOTER
			'type'     => 'number',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_footer_options',
		),
	) );
}
return $settings;
