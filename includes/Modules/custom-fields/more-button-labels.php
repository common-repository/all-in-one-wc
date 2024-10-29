<?php
/**
 * All In One For WooCommerce Settings - More Button Labels
 *
 * @package WordPress
 * @package WooCommerce
 */

return array(
	array(
		'title'    => __( 'Place order (Order now) Button', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_checkout_place_order_button_options',
	),
	array(
		'title'    => __( 'Text', 'all-in-one-wc' ),
		'desc'     => __( 'Leave blank for WooCommerce default.', 'all-in-one-wc' ),
		'desc_tip' => __( 'Button on the checkout page.', 'all-in-one-wc' ),
		'id'       => 'aiow_checkout_place_order_button_text',
		'default'  => '',
		'type'     => 'text',
	),
	array(
		'title'    => __( 'Override Default Text', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if button text is not changing for some payment gateway (e.g. PayPal).', 'all-in-one-wc' ),
		'id'       => 'aiow_checkout_place_order_button_override',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_checkout_place_order_button_options',
	),
);
