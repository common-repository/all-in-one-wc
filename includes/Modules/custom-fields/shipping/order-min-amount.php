<?php
/**
 * Shipping - Settings - Order Minimum Amount
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Order Minimum Amount', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you set minimum order amount.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_options',
	),
	array(
		'title'    => __( 'Amount', 'all-in-one-wc' ),
		'desc'     => __( 'Minimum order amount. Set to 0 to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount',
		'default'  => 0,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '0.0001', 'min'  => '0' ),
	),
	array(
		'title'    => __( 'Exclude Shipping from Cart Total', 'all-in-one-wc' ),
		'desc'     => __( 'Exclude', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_exclude_shipping',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Exclude Discounts from Cart Total', 'all-in-one-wc' ),
		'desc'     => __( 'Exclude', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_exclude_discounts',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'             => __( 'Exclude Discounts from Yith Gift Cards', 'all-in-one-wc' ),
		'desc'              => empty( $message = apply_filters( 'aiow_message', '', 'desc' ) ) ? __( 'Exclude', 'all-in-one-wc' ) : $message,
		'id'                => 'aiow_order_minimum_amount_exclude_yith_gift_card_discount',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'title'    => __( 'Error message', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Message to customer if order is below minimum amount. Default: You must have an order with a minimum of %s to place your order, your current order total is %s.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_error_message',
		'default'  => 'You must have an order with a minimum of %s to place your order, your current order total is %s.',
		'type'     => 'textarea',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Add notice to cart page also', 'all-in-one-wc' ),
		'desc'     => __( 'Add', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_cart_notice_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Message on cart page', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'desc_tip' => __( 'Message to customer if order is below minimum amount. Default: You must have an order with a minimum of %s to place your order, your current order total is %s.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_cart_notice_message',
		'default'  => 'You must have an order with a minimum of %s to place your order, your current order total is %s.',
		'type'     => 'textarea',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'      => 'width:50%;min-width:300px;',
	),
	array(
		'title'    => __( 'Advanced', 'all-in-one-wc' ),
		'desc'     => __( 'Cart notice method', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_cart_notice_function',
		'default'  => 'wc_print_notice',
		'type'     => 'select',
		'options'  => array(
			'wc_print_notice' => __( 'Print notice', 'all-in-one-wc' ),
			'wc_add_notice'   => __( 'Add notice', 'all-in-one-wc' ),
		),
	),
	array(
		'desc'     => __( 'Cart notice type', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_cart_notice_type',
		'default'  => 'notice',
		'type'     => 'select',
		'options'  => array(
			'notice' => __( 'Notice', 'all-in-one-wc' ),
			'error'  => __( 'Error', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Stop customer from seeing the Checkout page if minimum amount not reached', 'all-in-one-wc' ),
		'desc'     => __( 'Redirect back to Cart page', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_amount_stop_from_seeing_checkout',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_minimum_amount_options',
	),
	array(
		'title'    => __( 'Compatibility', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'Compatibility with other modules or plugins.', 'all-in-one-wc' ),
		'id'       => 'aiow_order_minimum_compatibility',
	),
	array(
		'title'             => __( 'WooCommerce Multilingual', 'all-in-one-wc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'aiow_message', '', 'desc' ) ) ? __( 'Enable', 'all-in-one-wc' ) : $message,
		'desc_tip'          => sprintf( __( 'Adds compatibility with <a href="%s" target="_blank">WooCommerce Multilingual</a> plugin.', 'all-in-one-wc' ), 'http://wpml.org/documentation/related-projects/woocommerce-multilingual/' ),
		'id'                => 'aiow_order_minimum_compatibility_wpml_multilingual',
		'default'           => 'no',
		'type'              => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_minimum_compatibility',
	),
	array(
		'title'    => __( 'Order Minimum Amount by User Role', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_order_minimum_amount_by_ser_role_options',
		'desc'     => sprintf( __( 'Custom roles can be added via "Add/Manage Custom Roles" tool in <a href="%s">General</a> module.', 'all-in-one-wc' ),
			admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=emails_and_misc&section=general' ) ),
	),
);
$c = array( 'guest', 'administrator', 'customer' );
$is_r = apply_filters( 'aiow_message', '', 'readonly' );
if ( '' == $is_r ) {
	$is_r = array();
}
foreach ( aiow_get_user_roles() as $role_key => $role_data ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $role_data['name'],
			'id'       => 'aiow_order_minimum_amount_by_user_role_' . $role_key,
			'default'  => 0,
			'type'     => 'number',
			'custom_attributes' => ( ! in_array( $role_key, $c ) ? array_merge( array( 'step' => '0.0001', 'min'  => '0', ), $is_r ) : array( 'step' => '0.0001', 'min'  => '0', ) ),
			'desc_tip' => ( ! in_array( $role_key, $c ) ? apply_filters( 'aiow_message', '', 'desc_no_link' ) : '' ),
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_order_minimum_amount_by_ser_role_options',
	),
) );
return $settings;
