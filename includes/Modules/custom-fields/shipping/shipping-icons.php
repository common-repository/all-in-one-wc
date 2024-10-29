<?php
/**
 * Shipping - Settings - Shipping Icons
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section will allow you to add icons for shipping method. Icons will be visible on cart and checkout pages.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_icons_options',
	),
	array(
		'title'    => __( 'Icon Position', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_icons_position',
		'default'  => 'before',
		'type'     => 'select',
		'options'  => array(
			'before' => __( 'Before label', 'all-in-one-wc' ),
			'after'  => __( 'After label', 'all-in-one-wc' ),
		),
	),
	array(
		'title'    => __( 'Icon Visibility', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_icons_visibility',
		'default'  => 'both',
		'type'     => 'select',
		'options'  => array(
			'both'          => __( 'On both cart and checkout pages', 'all-in-one-wc' ),
			'cart_only'     => __( 'Only on cart page', 'all-in-one-wc' ),
			'checkout_only' => __( 'Only on checkout page', 'all-in-one-wc' ),
		),
		'desc_tip' => __( 'Possible values: on both cart and checkout pages; only on cart page; only on checkout page', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Icon Style', 'all-in-one-wc' ),
		'desc_tip' => __( 'You can also style icons with CSS class "aiow_shipping_icon", or id "aiow_shipping_icon_method_id"', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_icons_style',
		'default'  => 'display:inline;',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_icons_options',
	),
	array(
		'title'    => __( 'Shipping Methods Icons', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_icons_methods_options',
	),
	array(
		'title'    => __( 'Use Shipping Instances', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want to use shipping methods instances instead of shipping methods.', 'all-in-one-wc' ) . ' ' .
			__( 'Save changes after enabling this option.', 'all-in-one-wc' ),
		'type'     => 'checkbox',
		'id'       => 'aiow_shipping_icons_use_shipping_instance',
		'default'  => 'no',
	),
);
$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_icons_use_shipping_instance', 'no' ) );
$shipping_methods       = ( $use_shipping_instances ? aiow_get_shipping_methods_instances( true ) : WC()->shipping()->get_shipping_methods() );
foreach ( $shipping_methods as $method ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => ( $use_shipping_instances ? $method['zone_name'] . ': ' . $method['shipping_method_title'] : $method->method_title ),
			'desc_tip' => __( 'Image URL', 'all-in-one-wc' ),
			'id'       => 'aiow_shipping_icon_' . ( $use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id ),
			'default'  => '',
			'type'     => 'text',
			'css'      => 'width:100%;',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_icons_methods_options',
	),
) );
return $settings;
