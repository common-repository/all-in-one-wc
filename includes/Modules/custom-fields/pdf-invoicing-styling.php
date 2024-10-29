<?php
/**
 * PDF Invoicing Styling - Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$is_full_fonts = aiow_check_and_maybe_download_tcpdf_fonts();
$settings      = array();
$invoice_types = ( 'yes' === aiow_option( 'aiow_invoicing_hide_disabled_docs_settings', 'no' ) ) ? aiow_get_enabled_invoice_types() : aiow_get_invoice_types();
foreach ( $invoice_types as $invoice_type ) {
	// Font family
	$font_family_option = ( $is_full_fonts ?
		array(
			'title'    => __( 'Font Family', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_general_font_family',
			'default'  => 'helvetica',
			'type'     => 'select',
			'options'  => apply_filters( 'aiow_pdf_invoicing_fonts', array(
				'courier'           => 'Courier',
				'helvetica'         => 'Helvetica',
				'times'             => 'Times',
				'dejavusans'        => 'DejaVu Sans (Unicode)',
				'droidsansfallback' => 'Droid Sans Fallback (Unicode)',
				'angsanaupc'        => 'AngsanaUPC (Unicode)',
				'cordiaupc'         => 'CordiaUPC (Unicode)',
				'thsarabun'         => 'THSarabunPSK (Unicode)',
				'stsongstdlight'    => 'STSong Light (Simp. Chinese)',
				'cid0ct'            => 'cid0ct (Chinese Traditional)',
			) ),
		) :
		array(
			'title'    => __( 'Font Family', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_general_font_family_fallback',
			'default'  => 'helvetica',
			'type'     => 'select',
			'options'  => array(
				'courier'           => 'Courier',
				'helvetica'         => 'Helvetica',
				'times'             => 'Times',
				'stsongstdlight'    => 'STSong Light (Simp. Chinese)',
			),
		)
	);
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'type'     => 'title',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_styling_options',
		),
		array(
			'title'    => __( 'CSS', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_css',
			'default'  => $this->get_default_css_template( $invoice_type['id'] ),
			'type'     => 'textarea',
			'css'      => 'width:100%;height:500px;',
		),
	),
	array( $font_family_option ),
	array(
		array(
			'title'    => __( 'Font Size', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_general_font_size',
			'default'  => 8,
			'type'     => 'number',
		),
		array(
			'title'    => __( 'Make Font Shadowed', 'all-in-one-wc' ),
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_general_font_shadowed',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_styling_options',
		),
	) );
}
return $settings;
