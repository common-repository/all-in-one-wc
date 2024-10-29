<?php
/**
 * Shipping - Settings - Shipping Calculator
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

return array(
	array(
		'title'    => __( 'Shipping Calculator Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_calculator_options',
	),
	array(
		'title'    => __( 'Enable City', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_enable_city',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable Postcode', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_enable_postcode',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Enable State', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_enable_state',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Force Block Open', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_enable_force_block_open',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => '',
		'desc'     => __( 'Calculate Shipping button', 'all-in-one-wc' ),
		'desc_tip' => __( 'When "Force Block Open" options is enabled, set Calculate Shipping button options.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_enable_force_block_open_button',
		'default'  => 'hide',
		'type'     => 'select',
		'options'  => array(
			'hide'    => __( 'Hide', 'all-in-one-wc' ),
			'noclick' => __( 'Make non clickable', 'all-in-one-wc' ),
		),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_calculator_options',
	),
	array(
		'title'    => __( 'Labels Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_calculator_labels_options',
	),
	array(
		'title'    => __( 'Labels', 'all-in-one-wc' ),
		'desc'     => __( 'Enable Section', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_labels_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Label for Calculate Shipping', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_label_calculate_shipping',
		'default'  => __( 'Calculate Shipping', 'all-in-one-wc' ),
		'type'     => 'text',
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc_no_link' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label for Update Totals', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_calculator_label_update_totals',
		'default'  => __( 'Update Totals', 'all-in-one-wc' ),
		'type'     => 'text',
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc_no_link' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_calculator_labels_options',
	),
);
