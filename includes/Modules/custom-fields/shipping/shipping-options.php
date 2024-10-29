<?php
/**
 * Shipping - Settings - Shipping Options
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Hide if Free Shipping is Available', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you hide other shipping options when free shipping is available on shop frontend.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_hide_if_free_available_options',
	),
	array(
		'title'    => __( 'Hide when free is available', 'all-in-one-wc' ),
		'desc'     => __( 'Enable section', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_hide_if_free_available_all',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'aiow_shipping_hide_if_free_available_type',
		'desc_tip' => sprintf( __( 'Available options: hide all; hide all except "Local Pickup"; hide "Flat Rate" only.', 'all-in-one-wc' ) ),
		'default'  => 'hide_all',
		'type'     => 'select',
		'options'  => array(
			'hide_all'            => __( 'Hide all', 'all-in-one-wc' ),
			'except_local_pickup' => __( 'Hide all except "Local Pickup"', 'all-in-one-wc' ),
			'flat_rate_only'      => __( 'Hide "Flat Rate" only', 'all-in-one-wc' ),
		),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Advanced: Filter Priority', 'all-in-one-wc' ),
		'desc_tip' => __( 'Set to zero to use the default priority.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_options_hide_free_shipping_filter_priority',
		'default'  => 0,
		'type'     => 'number',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_hide_if_free_available_options',
	),
);
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Free Shipping by Product', 'all-in-one-wc' ),
		'desc'     => __( 'In this section you can select products which grant free shipping when added to cart.', 'all-in-one-wc' ) . '<br>' .
			sprintf( __( 'Similar results can be achieved with %s module.', 'all-in-one-wc' ),
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=shipping_and_orders&section=shipping_by_products' ) . '">' .
					__( 'Shipping Methods by Products', 'all-in-one-wc' ) . '</a>' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_free_shipping_by_product_options',
	),
	array(
		'title'    => __( 'Free Shipping by Product', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_free_shipping_by_product_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Products', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_free_shipping_by_product_products',
		'default'  => '',
		'type'     => 'multiselect',
		'options'  => aiow_get_products(),
		'class'    => 'chosen_select',
	),
	array(
		'title'    => __( 'Type', 'all-in-one-wc' ),
		'desc_tip' => __( 'Select either <strong>all products</strong> or <strong>at least one product</strong> in cart must grant free shipping.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_free_shipping_by_product_type',
		'default'  => 'all',
		'type'     => 'select',
		'options'  => array(
			'all'          => __( 'All products in cart must grant free shipping', 'all-in-one-wc' ),
			'at_least_one' => __( 'At least one product in cart must grant free shipping', 'all-in-one-wc' ),
		),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_free_shipping_by_product_options',
	),
) );
$shipping_methods_opt = array_map( function ( $item ) {
	return $item->method_title;
}, WC()->shipping->get_shipping_methods() );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Show Only the Most Expensive Shipping', 'all-in-one-wc' ),
		'desc'     => __( 'In this section you can show only the most expensive shipping, ignoring other ones as you wish, like free shipping or local pickup.', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_most_expensive',
	),
	array(
		'title'    => __( 'Show Only the Most Expensive Shipping', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_most_expensive_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Ignored Shipping Methods', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_most_expensive_ignored_methods',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
		'desc_tip' => __( 'Use it if you\'d like to show the most expensive shipping method ignoring some other one.', 'all-in-one-wc' ),
		'default'  => array( 'free_shipping' ),
		'type'     => 'multiselect',
		'options'  => $shipping_methods_opt,
		'class'    => 'chosen_select',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_most_expensive',
	),
) );
return $settings;


