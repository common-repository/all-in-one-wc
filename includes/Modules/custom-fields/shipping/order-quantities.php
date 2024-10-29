<?php
/**
 * Shipping - Settings - Order Min/Max Quantities
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$qty_step_settings = ( 'yes' === aiow_option( 'aiow_order_quantities_decimal_qty_enabled', 'no' ) ? '0.000001' : '1' );

return array(
	array(
		'title'    => __( 'General Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_quantities_general_options',
	),
	array(
		'title'    => __( 'Decimal Quantities', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Save module\'s settings after enabling this option, so you could enter decimal quantities in step, min and/or max quantity options.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_decimal_qty_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Force Initial Quantity on Single Product Page', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_force_on_single',
		'default'  => 'disabled',
		'type'     => 'select',
		'options'  => array(
			'disabled' => __( 'Do not force', 'all-in-one-wc' ),
			'min'      => __( 'Force to min quantity', 'all-in-one-wc' ),
			'max'      => __( 'Force to max quantity', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Enable Cart Notices', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_cart_notice_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Stop Customer from Seeing Checkout on Wrong Quantities', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Will be redirected to cart page.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_stop_from_seeing_checkout',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Variable Products', 'all-in-one-wc' ),
		'desc'     => '<br>' . __( 'Action on variation change', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_variable_variation_change',
		'default'  => 'do_nothing',
		'type'     => 'select',
		'options'  => array(
			'do_nothing'   => __( 'Do nothing', 'all-in-one-wc' ),
			'reset_to_min' => __( 'Reset to min quantity', 'all-in-one-wc' ),
			'reset_to_max' => __( 'Reset to max quantity', 'all-in-one-wc' ),
		),
	),
	array(
		'desc'     => __( 'Force on add to cart', 'all-in-one-wc' ),
		'desc_tip' => __( 'Force quantity correction on add to cart button click', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_variable_force_on_add_to_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_quantities_general_options',
	),
	array(
		'title'    => __( 'Minimum Quantity Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_quantities_min_options',
	),
	array(
		'title'    => __( 'Minimum Quantity', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_quantities_min_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Cart Total Quantity', 'all-in-one-wc' ),
		'desc_tip' => __( 'Set to zero to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_min_cart_total_quantity',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0, 'step' => $qty_step_settings ),
	),
	array(
		'title'    => __( 'Message - Cart Total Quantity', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%min_cart_total_quantity%', '%cart_total_quantity%' ) ),
		'id'       => 'aiow_order_quantities_min_cart_total_message',
		'default'  => __( 'Minimum allowed order quantity is %min_cart_total_quantity%. Your current order quantity is %cart_total_quantity%.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Per Item Quantity', 'all-in-one-wc' ),
		'desc_tip' => __( 'Set to zero to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_min_per_item_quantity',
		'default'  => 0,
		'type'     => 'number',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => ( '' === apply_filters( 'aiow_message', '', 'readonly' ) ? array( 'min' => 0, 'step' => $qty_step_settings ) : apply_filters( 'aiow_message', '', 'readonly' ) ),
	),
	array(
		'title'    => __( 'Per Item Quantity on Per Product Basis', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_order_quantities_min_per_item_quantity_per_product',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Message - Per Item Quantity', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%product_title%', '%min_per_item_quantity%', '%item_quantity%' ) ),
		'id'       => 'aiow_order_quantities_min_per_item_message',
		'default'  => __( 'Minimum allowed quantity for %product_title% is %min_per_item_quantity%. Your current item quantity is %item_quantity%.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_quantities_min_options',
	),
	array(
		'title'    => __( 'Maximum Quantity Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_quantities_max_options',
	),
	array(
		'title'    => __( 'Maximum Quantity', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_quantities_max_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Cart Total Quantity', 'all-in-one-wc' ),
		'desc_tip' => __( 'Set to zero to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_max_cart_total_quantity',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0, 'step' => $qty_step_settings ),
	),
	array(
		'title'    => __( 'Message - Cart Total Quantity', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%max_cart_total_quantity%', '%cart_total_quantity%' ) ),
		'id'       => 'aiow_order_quantities_max_cart_total_message',
		'default'  => __( 'Maximum allowed order quantity is %max_cart_total_quantity%. Your current order quantity is %cart_total_quantity%.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Per Item Quantity', 'all-in-one-wc' ),
		'desc_tip' => __( 'Set to zero to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_max_per_item_quantity',
		'default'  => 0,
		'type'     => 'number',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => ( '' === apply_filters( 'aiow_message', '', 'readonly' ) ? array( 'min' => 0, 'step' => $qty_step_settings ) : apply_filters( 'aiow_message', '', 'readonly' ) ),
	),
	array(
		'title'    => __( 'Per Item Quantity on Per Product Basis', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_order_quantities_max_per_item_quantity_per_product',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Message - Per Item Quantity', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%product_title%', '%max_per_item_quantity%', '%item_quantity%' ) ),
		'id'       => 'aiow_order_quantities_max_per_item_message',
		'default'  => __( 'Maximum allowed quantity for %product_title% is %max_per_item_quantity%. Your current item quantity is %item_quantity%.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_quantities_max_options',
	),
	array(
		'title'    => __( 'Quantity Step Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_quantities_step_options',
	),
	array(
		'title'    => __( 'Quantity Step', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_order_quantities_step_section_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Step', 'all-in-one-wc' ),
		'desc_tip' => __( 'Ignored if set to zero.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_step',
		'default'  => 1,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 0, 'step' => $qty_step_settings ),
	),
	array(
		'title'    => __( 'Per Product', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_order_quantities_step_per_product',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Additional Validation', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_step_additional_validation_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Message', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%product_title%', '%required_step%', '%item_quantity%' ) ),
		'id'       => 'aiow_order_quantities_step_message',
		'default'  => __( 'Required step for %product_title% is %required_step%. Your current item quantity is %item_quantity%.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_quantities_step_options',
	),
	array(
		'title'    => __( '"Single Item Cart" Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_quantities_single_item_cart_options',
	),
	array(
		'title'    => __( 'Enable "Single Item Cart" Mode', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'desc_tip' => __( 'When enabled, only one item will be allowed to be added to the cart (quantity is not checked).', 'all-in-one-wc' ) . ' ' .
			apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_order_quantities_single_item_cart_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Message', 'all-in-one-wc' ),
		'id'       => 'aiow_order_quantities_single_item_cart_message',
		'default'  => __( 'Only one item can be added to the cart. Clear the cart or finish the order, before adding another item to the cart.', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_quantities_single_item_cart_options',
	),
);