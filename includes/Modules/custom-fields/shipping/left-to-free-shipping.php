<?php
/**
 * Shipping - Settings - Left to Free Shipping
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

return array(
	array(
		'title'    => __( 'Left to Free Shipping Info Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you enable info on cart, mini cart and checkout pages.', 'all-in-one-wc' ) . '<br>' . '<br>' .
			sprintf( __( 'You can also use <em>Left to Free Shipping</em> widget, %s shortcode or %s function.', 'all-in-one-wc' ),
				'<code>[aiow_get_left_to_free_shipping content=""]</code>',
				'<code>aiow_get_left_to_free_shipping( $content );</code>' ) . '<br>' . '<br>' .
			sprintf( __( 'In content replaced values are: %s, %s and %s.', 'all-in-one-wc' ),
				'<code>%left_to_free%</code>',
				'<code>%free_shipping_min_amount%</code>',
				'<code>%cart_total%</code>' ),
		'id'       => 'aiow_shipping_left_to_free_info_options',
	),
	array(
		'title'    => __( 'Info on Cart', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_enabled_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Content', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can use HTML and/or shortcodes (e.g. [aiow_wpml]) here.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_content_cart',
		'default'  => __( '%left_to_free% left to free shipping', 'all-in-one-wc' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'desc'     => __( 'Position', 'all-in-one-wc' ),
		'desc_tip' => __( 'Please note, that depending on the "Position" you select, your customer may have to reload the cart page to see the updated left to free shipping value. For example, if you select "After cart totals" position, then left to free shipping value will be updated as soon as customer updates the cart. However if you select "After cart" position instead – message will not be updated, and customer will have to reload the page. In other words, message position should be inside that page part that is automatically updated on cart update.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_position_cart',
		'default'  => 'woocommerce_after_cart_totals',
		'type'     => 'select',
		'options'  => aiow_get_cart_filters(),
	),
	array(
		'desc'     => __( 'Position Order (Priority)', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_priority_cart',
		'default'  => 10,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Info on Mini Cart', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_enabled_mini_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
	),
	array(
		'desc'     => __( 'Content', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can use HTML and/or shortcodes (e.g. [aiow_wpml]) here.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_content_mini_cart',
		'default'  => __( '%left_to_free% left to free shipping', 'all-in-one-wc' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'desc'     => __( 'Position', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_position_mini_cart',
		'default'  => 'woocommerce_after_mini_cart',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_mini_cart'                    => __( 'Before mini cart', 'all-in-one-wc' ),
			'woocommerce_widget_shopping_cart_before_buttons' => __( 'Before buttons', 'all-in-one-wc' ),
			'woocommerce_after_mini_cart'                     => __( 'After mini cart', 'all-in-one-wc' ),
		),
	),
	array(
		'desc'     => __( 'Position Order (Priority)', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_priority_mini_cart',
		'default'  => 10,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Info on Checkout', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_enabled_checkout',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'desc_tip' => apply_filters( 'aiow_message', '', 'desc' ),
	),
	array(
		'desc'     => __( 'Content', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can use HTML and/or shortcodes (e.g. [aiow_wpml]) here.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_content_checkout',
		'default'  => __( '%left_to_free% left to free shipping', 'all-in-one-wc' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'desc'     => __( 'Position', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_position_checkout',
		'default'  => 'woocommerce_checkout_after_order_review',
		'type'     => 'select',
		'options'  => array(
			'woocommerce_before_checkout_form'              => __( 'Before checkout form', 'all-in-one-wc' ),
			'woocommerce_checkout_before_customer_details'  => __( 'Before customer details', 'all-in-one-wc' ),
			'woocommerce_checkout_billing'                  => __( 'Billing', 'all-in-one-wc' ),
			'woocommerce_checkout_shipping'                 => __( 'Shipping', 'all-in-one-wc' ),
			'woocommerce_checkout_after_customer_details'   => __( 'After customer details', 'all-in-one-wc' ),
			'woocommerce_checkout_before_order_review'      => __( 'Before order review', 'all-in-one-wc' ),
			'woocommerce_checkout_order_review'             => __( 'Order review', 'all-in-one-wc' ),
			'woocommerce_review_order_before_shipping'      => __( 'Order review: Before shipping', 'all-in-one-wc' ),
			'woocommerce_review_order_after_shipping'       => __( 'Order review: After shipping', 'all-in-one-wc' ),
			'woocommerce_review_order_before_submit'        => __( 'Order review: Payment: Before submit button', 'all-in-one-wc' ),
			'woocommerce_review_order_after_submit'         => __( 'Order review: Payment: After submit button', 'all-in-one-wc' ),
			'woocommerce_checkout_after_order_review'       => __( 'After order review', 'all-in-one-wc' ),
			'woocommerce_after_checkout_form'               => __( 'After checkout form', 'all-in-one-wc' ),
		),
	),
	array(
		'desc'     => __( 'Position Order (Priority)', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_priority_checkout',
		'default'  => 10,
		'type'     => 'number',
	),
	array(
		'title'    => __( 'Message on Free Shipping Reached', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can use HTML and/or shortcodes (e.g. [aiow_wpml]) here.', 'all-in-one-wc' ) . ' ' .
			__( 'Set empty to disable.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_left_to_free_info_content_reached',
		'default'  => __( 'You have Free delivery', 'all-in-one-wc' ),
		'type'     => 'textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_left_to_free_info_options',
	),
);
