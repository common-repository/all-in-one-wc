<?php
/**
 * Shipping - Settings - Max Products per User
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

return array(
	array(
		'title'    => __( 'All Products', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_max_products_per_user_global_options',
	),
	array(
		'title'    => __( 'All Products', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_max_products_per_user_global_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Maximum Allowed Each Product\'s Quantity per User', 'all-in-one-wc' ),
		'id'       => 'aiow_max_products_per_user_global_max_qty',
		'default'  => 1,
		'type'     => 'number',
		'custom_attributes' => array( 'min' => 1 ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_max_products_per_user_global_options',
	),
	array(
		'title'    => __( 'Per Product', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_max_products_per_user_local_options',
	),
	array(
		'title'    => __( 'Per Product', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable section', 'all-in-one-wc' ) . '</strong>',
		'desc_tip' => __( 'This will add new meta box to each product\'s edit page.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_max_products_per_user_local_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_max_products_per_user_local_options',
	),
	array(
		'title'    => __( 'General Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_max_products_per_user_general_options',
	),
	array(
		'title'    => __( 'Order Status', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets when (i.e. on which order status) users\' quantities should be updated.', 'all-in-one-wc' ) . ' ' .
			__( 'You can select multiple order status here - quantities will be updated only once, on whichever status is triggered first.', 'all-in-one-wc' ) . ' ' .
			__( 'If no status are selected - "Completed" order status is used.', 'all-in-one-wc' ),
		'id'       => 'aiow_max_products_per_user_order_status',
		'default'  => array( 'wc-completed' ),
		'options'  => aiow_get_order_statuses( false ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
	),
	array(
		'title'    => __( 'Customer Message', 'all-in-one-wc' ),
		'desc'     => aiow_message_replaced_values( array( '%max_qty%', '%product_title%', '%qty_already_bought%', '%remaining_qty%' ) ),
		'id'       => 'aiow_max_products_per_user_message',
		'default'  => __( 'You can only buy maximum %max_qty% pcs. of %product_title% (you already bought %qty_already_bought% pcs.).', 'all-in-one-wc' ),
		'type'     => 'custom_textarea',
		'css'      => 'width:100%;height:100px;',
	),
	array(
		'title'    => __( 'Block Add to Cart', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will stop customer from adding product to cart on exceeded quantities.', 'all-in-one-wc' ),
		'id'       => 'aiow_max_products_per_user_stop_from_adding_to_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Block Checkout Page', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'This will stop customer from accessing the checkout page on exceeded quantities. Customer will be redirected to the cart page.', 'all-in-one-wc' ),
		'id'       => 'aiow_max_products_per_user_stop_from_seeing_checkout',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Calculate Data', 'all-in-one-wc' ),
		'id'       => 'aiow_max_products_per_user_calculate_data',
		'default'  => '',
		'type'     => 'custom_link',
		'link'     => '<a class="button" href="' .
			add_query_arg( 'aiow_max_products_per_user_calculate_data', '1', remove_query_arg( 'aiow_max_products_per_user_calculate_data_finished' ) ) . '">' .
				__( 'Calculate Data', 'all-in-one-wc' ) .
			'</a>',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_max_products_per_user_general_options',
	),
);
