<?php
/**
 * PDF Invoicing - Advanced Settings.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$is_full_fonts = aiow_check_and_maybe_download_tcpdf_fonts();
if ( 'yes' === aiow_option( 'aiow_invoicing_fonts_manager_do_not_download', 'no' ) ) {
	$fonts_manager_desc = __( 'Fonts download is disabled.', 'all-in-one-wc' );
} else {
	if ( $is_full_fonts ) {
		$fonts_manager_desc = __( 'Fonts are up to date.', 'all-in-one-wc' ) . ' ' . sprintf(
			__( 'Latest successful download or version check was on %s.', 'all-in-one-wc' ),
			date( 'Y-m-d H:i:s', aiow_option( 'aiow_invoicing_fonts_version_timestamp', null ) )
		);
	} else {
		$fonts_manager_desc = __( 'Fonts are NOT up to date. Please try downloading by pressing the button below.', 'all-in-one-wc' );
		if ( null != aiow_option( 'aiow_invoicing_fonts_version', null ) ) {
			$fonts_manager_desc .= ' ' . sprintf(
				__( 'Latest successful downloaded version is %s.', 'all-in-one-wc' ),
				get_option( 'aiow_invoicing_fonts_version', null )
			);
		}
		if ( null != aiow_option( 'aiow_invoicing_fonts_version_timestamp', null ) ) {
			$fonts_manager_desc .= ' ' . sprintf(
				__( 'Latest download executed on %s.', 'all-in-one-wc' ),
				date( 'Y-m-d H:i:s', aiow_option( 'aiow_invoicing_fonts_version_timestamp', null ) )
			);
		}
	}
}

return array(
	array(
		'type'     => 'title',
		'title'    => __( 'Advanced Options', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_advanced_options',
	),
	array(
		'title'    => __( 'Hide Disabled Docs Settings', 'all-in-one-wc' ),
		'desc'     => __( 'Hide', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_hide_disabled_docs_settings',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Replace Admin Order Search with Invoice Search', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_admin_search_by_invoice',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Default Images Directory', 'all-in-one-wc' ),
		'desc'     => '<br>' . __( 'Default images directory in TCPDF library (K_PATH_IMAGES).', 'all-in-one-wc' ),
		'desc_tip' => __( 'Try changing this if you have issues displaying images in page background or header.', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_general_header_images_path', // mislabelled, should be `aiow_invoicing_general_images_path`
		'default'  => 'document_root',
		'type'     => 'select',
		'options'  => array(
			'empty'         => __( 'Empty', 'all-in-one-wc' ),
			'tcpdf_default' => __( 'TCPDF Default', 'all-in-one-wc' ),
			'abspath'       => __( 'ABSPATH', 'all-in-one-wc' ),       // . ': ' . ABSPATH,
			'document_root' => __( 'DOCUMENT_ROOT', 'all-in-one-wc' ), // . ': ' . $_SERVER['DOCUMENT_ROOT'],
		),
	),
	array(
		'title'    => __( 'Temp Directory', 'all-in-one-wc' ),
		'desc_tip' => __( 'Leave blank to use the default temp directory.', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_general_tmp_dir',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Disable Saving PDFs in Temp Directory', 'all-in-one-wc' ),
		'desc_tip' => __( 'Please note that attaching invoices to emails and generating invoices report zip will stop working, if you enable this checkbox.', 'all-in-one-wc' ),
		'desc'     => __( 'Disable', 'all-in-one-wc' ),
		'id'       => 'aiow_general_advanced_disable_save_sys_temp_dir', // mislabelled, should be `aiow_invoicing_advanced_disable_save_sys_temp_dir`
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Prevent Output Buffer', 'all-in-one-wc' ),
		'desc' => __( 'Returns the content of output buffering instead of displaying it', 'all-in-one-wc' ),
		'id'       => 'aiow_general_advanced_disable_output_buffer',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Internal Encoding', 'all-in-one-wc' ),
		'desc_tip' => __( 'Sets internal character encoding.', 'all-in-one-wc' ).'<br />'.__( 'e.g: UTF-8, iso-8859-1', 'all-in-one-wc' ),
		'id'       => 'aiow_general_advanced_mb_internal_encoding',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'WooCommerce Extra Product Options on Item Name', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => sprintf( __( 'Displays some info from <a href="%s" target="_blank">WooCommerce Extra Product Options</a> on <code>item_name</code> parameter from <code>aiow_order_items_table</code>.', 'all-in-one-wc' ), 'https://codecanyon.net/item/woocommerce-extra-product-options/7908619' ).'<br />'.__( 'Probably you\'ll want it disabled and use the <code>item_meta</code> parameter instead.', 'all-in-one-wc' ),
		'id'       => 'aiow_general_advanced_wcepo_enable',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Item Meta Separator', 'all-in-one-wc' ),
		'desc'     => __( 'Separator used on <code>item_meta</code> parameter from <code>aiow_order_items_table</code>', 'all-in-one-wc' ),
		'id'       => 'aiow_general_item_meta_separator',
		'aiow_raw'  => true,
		'default'  => ', ',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_pdf_invoicing_advanced_options',
	),
	array(
		'type'     => 'title',
		'title'    => __( 'Item Name as Product Title', 'all-in-one-wc' ),
		'desc'     => __( 'Replaces <code>item_name</code> by product title when using <code>[aiow_order_items_table columns="item_name"]</code>.', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_advanced_item_name_as_prod_title',
	),
	array(
		'title'    => __( 'Enable', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_advanced_item_name_as_prod_title_enable',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Translate WPML Title', 'all-in-one-wc' ),
		'desc'              => empty( $message = apply_filters( 'aiow_message', '', 'desc' ) ) ? __( 'Enable', 'all-in-one-wc' ) : $message,
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'desc_tip'          => __( 'Tries to translate the product title to the current WPML language.', 'all-in-one-wc' ),
		'id'                => 'aiow_pdf_invoicing_advanced_item_name_as_prod_title_wpml',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_pdf_invoicing_advanced_item_name_as_prod_title',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_invoicing_fonts_manager_styling_options',
	),
	array(
		'title'    => __( 'General Display Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_invoicing_general_display_options',
	),
	array(
		'title'    => __( 'Add PDF Invoices Meta Box to Admin Edit Order Page', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_add_order_meta_box',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Open docs in new window', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_order_meta_box_open_in_new_window',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Add editable numbers and dates', 'all-in-one-wc' ),
		'id'       => 'aiow_invoicing_add_order_meta_box_numbering',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_invoicing_general_display_options',
	),
	array(
		'title'    => __( 'Report Tool Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_pdf_invoicing_report_tool_options',
	),
	array(
		'title'    => __( 'Reports Filename', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%site%', '%invoice_type%', '%year%', '%month%' ) ),
		'id'       => 'aiow_pdf_invoicing_report_tool_filename',
		'default'  => '%site%-%invoice_type%-%year%_%month%',
		'type'     => 'text',
		'class'    => 'widefat',
	),
	array(
		'title'    => __( 'Report Columns', 'all-in-one-wc' ),
		'desc_tip' => __( 'Leave blank to show all columns.', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_report_tool_columns',
		'default'  => $this->get_report_default_columns(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => $this->get_report_columns(),
	),
	array(
		'title'    => __( 'Tax Percent Precision', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_report_tool_tax_percent_precision',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0 ),
	),
	array(
		'title'    => __( 'CSV Separator', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_report_tool_csv_separator',
		'default'  => ';',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'CSV UTF-8 BOM', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_report_tool_csv_add_utf_8_bom',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Replace Periods with Commas in CSV Data', 'all-in-one-wc' ),
		'desc'     => __( 'Replace', 'all-in-one-wc' ),
		'id'       => 'aiow_pdf_invoicing_report_tool_csv_replace_periods_w_commas',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_pdf_invoicing_report_tool_options',
	),
);
