<?php
/**
 * Shipping - Settings - Shipping Methods by Min/Max Order Amount
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_by_order_amount_use_shipping_instance', 'no' ) );
$settings = array(
	array(
		'title'    => __( 'General Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_by_order_amount_general_options',
	),
	array(
		'title'    => __( 'Use Shipping Instances', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want to use shipping methods instances instead of shipping methods.', 'all-in-one-wc' ) . ' ' .
			__( 'Save changes after enabling this option.', 'all-in-one-wc' ),
		'type'     => 'checkbox',
		'id'       => 'aiow_shipping_by_order_amount_use_shipping_instance',
		'default'  => 'no',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_by_order_amount_general_options',
	),
	array(
		'title'   => __( 'Shipping Methods by Min/Max Order Amount', 'all-in-one-wc' ),
		'type'    => 'title',
		'desc'    => __( 'Set to zero to disable.', 'all-in-one-wc' ),
		'id'      => 'aiow_shipping_by_order_amount_options',
	),
);
$shipping_methods = ( $use_shipping_instances ? aiow_get_shipping_methods_instances( true ) : WC()->shipping()->load_shipping_methods() );
foreach ( $shipping_methods as $method ) {
	$method_id = ( $use_shipping_instances ? $method['shipping_method_id'] : $method->id );
	if ( ! in_array( $method_id, array( 'flat_rate', 'free_shipping' ) ) ) {
		$custom_attributes = apply_filters( 'aiow_message', '', 'disabled' );
		if ( '' == $custom_attributes ) {
			$custom_attributes = array();
		}
		$desc_tip = apply_filters( 'aiow_message', '', 'desc_no_link' );
	} else {
		$custom_attributes = array();
		$desc_tip = '';
	}
	$custom_attributes = array_merge( $custom_attributes, array( 'min' => 0 ) );
	$settings = array_merge( $settings, array(
		array(
			'title'     => ( $use_shipping_instances ? $method['zone_name'] . ': ' . $method['shipping_method_title']: $method->get_method_title() ),
			'desc_tip'  => $desc_tip,
			'desc'      => '<br>' . __( 'Minimum order amount', 'all-in-one-wc' ),
			'id'        => 'aiow_shipping_by_order_amount_min_' . ( $use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id ),
			'default'   => 0,
			'type'      => 'number',
			'custom_attributes' => $custom_attributes,
		),
		array(
			'desc_tip'  => $desc_tip,
			'desc'      => '<br>' . __( 'Maximum order amount', 'all-in-one-wc' ),
			'id'        => 'aiow_shipping_by_order_amount_max_' . ( $use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id ),
			'default'   => 0,
			'type'      => 'number',
			'custom_attributes' => $custom_attributes,
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'  => 'sectionend',
		'id'    => 'aiow_shipping_by_order_amount_options',
	),
) );
return $settings;
