<?php
/**
 * PDF Invoicing Display - Settings
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array();
$invoice_types = ( 'yes' === aiow_option( 'aiow_invoicing_hide_disabled_docs_settings', 'no' ) ) ? aiow_get_enabled_invoice_types() : aiow_get_invoice_types();
foreach ( $invoice_types as $invoice_type ) {
	$document_number_shortode = ( isset( $invoice_type['is_custom_doc'] ) && true === $invoice_type['is_custom_doc'] ?
		'[aiow_custom_doc_number doc_nr="' . $invoice_type['custom_doc_nr'] . '"]' : '[aiow_' . $invoice_type['id'] . '_number]' );
	$settings = array_merge( $settings, array(
		array(
			'title'    => $invoice_type['title'],
			'type'     => 'title',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_display_options',
		),
		array(
			'title'    => __( 'Admin Title', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_title',
			'default'  => $invoice_type['title'],
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Admin\'s "Orders" Page', 'all-in-one-wc' ),
			'desc'     => __( 'Add Column', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_page_column',
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => '',
			'desc'     => __( 'Column Title', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_page_column_text',
			'default'  => $invoice_type['title'],
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'desc'     => __( 'Add "View" button', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_view_btn',
			'default'  => 'no',
			'type'     => 'checkbox',
			'checkboxgroup' => 'start',
		),
		array(
			'desc'     => __( 'Add "Create" button', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_create_btn',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'checkboxgroup' => '',
		),
		array(
			'desc'     => __( 'Add "Delete" button', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_delete_btn',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'checkboxgroup' => '',
		),
		array(
			'desc'     => __( '"Create" button requires confirmation', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_create_btn_confirm',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'checkboxgroup' => '',
		),
		array(
			'desc'     => __( '"Delete" button requires confirmation', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_admin_orders_delete_btn_confirm',
			'default'  => 'yes',
			'type'     => 'checkbox',
			'checkboxgroup' => 'end',
		),
		array(
			'title'    => __( 'Thank You Page', 'all-in-one-wc' ),
			'desc'     => __( 'Add link', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_enabled_on_thankyou_page',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Link Text', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_thankyou_page_link_text',
			'default'  => $invoice_type['title'],
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'desc'     => __( 'HTML Template', 'all-in-one-wc' ) . '. ' . aiow_message_replaced_values( array( '%link%' ) ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_thankyou_page_template',
			'default'  => '<p><strong>' . sprintf( __( 'Your %s:', 'all-in-one-wc' ), $invoice_type['title'] ) . ' </strong> %link%</p>',
			'type'     => 'custom_textarea',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Customer\'s "My Account" Page', 'all-in-one-wc' ),
			'desc'     => __( 'Add link', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_enabled_for_customers',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'Link Text', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_link_text',
			'default'  => $invoice_type['title'],
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Enable "Save as"', 'all-in-one-wc' ),
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'desc_tip' => __( 'Enable "save as" pdf instead of view pdf in browser', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_save_as_enabled',
			'default'  => 'no',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'PDF File Name', 'all-in-one-wc' ),
			'desc'     => sprintf( __( 'Enter file name for PDF documents. You can use shortcodes here, e.g. %s.', 'all-in-one-wc' ),
				'<code>' . $document_number_shortode . '</code>' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_file_name',
			'default'  => $document_number_shortode,
			'type'     => 'text',
			'class'    => 'widefat',
		),
		array(
			'title'    => __( 'Allowed User Roles', 'all-in-one-wc' ),
			'desc'     => __( 'If set to empty - Administrator role will be used.', 'all-in-one-wc' ),
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_roles',
			'default'  => array( 'administrator', 'shop_manager' ),
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => aiow_get_user_roles_options(),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_invoicing_' . $invoice_type['id'] . '_display_options',
		),
	) );
}
return $settings;
