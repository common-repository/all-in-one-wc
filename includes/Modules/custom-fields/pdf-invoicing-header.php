<?php
/**
 * PDF Invoicing Header - Settings.
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
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_options',
		),
		array(
			'title'    => __( 'Enable Header', 'all-in-one-wc' ),
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_enabled',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'Header Image', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_image',
			'default'  => '',
			'type'     => 'text',
			'desc'     => sprintf(
				__( 'Enter a local URL to an image you want to show in the invoice\'s header. Upload your image using the <a href="%s">media uploader</a>.', 'all-in-one-wc' ),
					admin_url( 'media-new.php' ) ) .
				aiow_get_invoicing_current_image_path_desc( 'aiow_invoicing_' . $invoice_type['id'] . '_header_image' ) . '<br>' .
				sprintf( __( 'If you are experiencing issues with displaying header image, please try setting different values for the "Advanced: Default Images Directory" option in %s.', 'all-in-one-wc' ),
					'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=pdf_invoicing&section=pdf_invoicing_advanced' ) . '">' .
						__( 'PDF Invoicing & Packing Slips > Advanced', 'all-in-one-wc' ) .
					'</a>' ),
			'desc_tip' => __( 'Leave blank to disable', 'all-in-one-wc' ),
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Header Image Width in mm', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_image_width_mm',
			'default'  => 50,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Header Title', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_title_text',
			'default'  => $invoice_type['title'],
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Header Text', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_text',
			'default'  => __( 'Company Name', 'all-in-one-wc' ),
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Header Text Color', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_text_color',
			'default'  => '#cccccc',
			'type'     => 'color',
			'css'      => 'width:6em;',
		),
		array(
			'title'    => __( 'Header Line Color', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_line_color',
			'default'  => '#cccccc',
			'type'     => 'color',
			'css'      => 'width:6em;',
		),
		array(
			'title'    => __( 'Header Margin', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_header',
			'default'  => 10, // PDF_MARGIN_HEADER
			'type'     => 'number',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_header_options',
		),
	) );
}
return $settings;
