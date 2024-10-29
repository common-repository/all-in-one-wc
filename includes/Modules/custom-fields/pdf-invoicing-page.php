<?php
/**
 * PDF Invoicing Page - Settings
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
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_options',
		),
		array(
			'title'    => __( 'Page Orientation', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_orientation',
			'default'  => 'P',
			'type'     => 'select',
			'options'  => array(
				'P' => __( 'Portrait', 'all-in-one-wc' ),
				'L' => __( 'Landscape', 'all-in-one-wc' ),
			),
		),
		array(
			'title'    => __( 'Page Format', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_format',
			'default'  => 'A4', // PDF_PAGE_FORMAT
			'type'     => 'select',
			'options'  => array_replace( array( 'custom' => __( 'Custom', 'all-in-one-wc' ) ), $this->get_page_formats() ),
		),
		array(
			'desc'     => __( 'Custom: width (millimeters)', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_format_custom_width',
			'default'  => '0',
			'type'     => 'number',
			'custom_attributes' => array( 'min' => 0 ),
		),
		array(
			'desc'     => __( 'Custom: height (millimeters)', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_format_custom_height',
			'default'  => '0',
			'type'     => 'number',
			'custom_attributes' => array( 'min' => 0 ),
		),
		array(
			'title'    => __( 'Margin Left', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_left',
			'default'  => 15, // PDF_MARGIN_LEFT,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Margin Right', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_right',
			'default'  => 15, // PDF_MARGIN_RIGHT,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Margin Top', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_top',
			'default'  => 27, // PDF_MARGIN_TOP,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Margin Bottom', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_margin_bottom',
			'default'  => 10, // PDF_MARGIN_BOTTOM,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Background Image', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_background_image',
			'default'  => '',
			'type'     => 'text',
			'desc'     => sprintf( __( 'Enter a local URL to an image. Upload your image using the <a href="%s">media uploader</a>.', 'all-in-one-wc' ),
					admin_url( 'media-new.php' ) ) .
				aiow_get_invoicing_current_image_path_desc( 'aiow_invoicing_' . $invoice_type['id'] . '_background_image' ) . '<br>' .
				sprintf( __( 'If you are experiencing issues with displaying background image, please try setting different values for the "Advanced: Default Images Directory" option in %s.', 'all-in-one-wc' ),
					'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=pdf_invoicing&section=pdf_invoicing_advanced' ) . '">' .
						__( 'PDF Invoicing & Packing Slips > Advanced', 'all-in-one-wc' ) .
					'</a>' ),
			'desc_tip' => __( 'Leave blank to disable', 'all-in-one-wc' ),
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Parse Background Image URL', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_background_image_parse',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'desc_tip' => __( 'Converts the Background Image URL to its local path.', 'all-in-one-wc' ) . '<br>' .__( 'If you are experiencing issues with displaying background image, please try to disable this option', 'all-in-one-wc' ),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_page_options',
		),
	) );
}
return $settings;
