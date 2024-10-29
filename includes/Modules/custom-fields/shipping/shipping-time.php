<?php
/**
 * Shipping - Settings - Shipping Time
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_time_use_shipping_instance', 'no' ) );
$use_shipping_classes   = ( 'yes' === apply_filters( 'aiow_option', 'no', aiow_option( 'aiow_shipping_time_use_shipping_classes', 'no' ) ) );
$shipping_methods       = ( $use_shipping_instances ? aiow_get_shipping_methods_instances( true ) : WC()->shipping()->load_shipping_methods() );
$shipping_classes_data  = ( $use_shipping_classes ? aiow_get_shipping_classes() : array( '' => '' ) );
$settings = array();
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'General Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_time_general_options',
	),
	array(
		'title'    => __( 'Use Shipping Instances', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want to use shipping methods instances instead of shipping methods.', 'all-in-one-wc' ) . ' ' .
			__( 'Save changes after enabling this option.', 'all-in-one-wc' ),
		'type'     => 'checkbox',
		'id'       => 'aiow_shipping_time_use_shipping_instance',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'Use Product Shipping Classes', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want to set options for each shipping class separately.', 'all-in-one-wc' ) . ' ' .
			__( 'Save changes after enabling this option.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc' ),
		'type'     => 'checkbox',
		'id'       => 'aiow_shipping_time_use_shipping_classes',
		'default'  => 'no',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_time_general_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Shipping Time Options', 'all-in-one-wc' ),
		'desc'     => __( 'Set estimated shipping time in <strong>days</strong>.', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_time_options',
	),
) );
foreach ( $shipping_methods as $method ) {
	$method_id = ( $use_shipping_instances ? $method['shipping_method_id'] : $method->id );
	foreach ( $shipping_classes_data as $shipping_class_id => $shipping_class_name ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => ( $use_shipping_instances ? $method['zone_name'] . ': ' . $method['shipping_method_title']: $method->get_method_title() ),
				'desc'     => ( $use_shipping_classes ? $shipping_class_name : '' ),
				'id'       => 'aiow_shipping_time_' .
					( $use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id ) .
					( $use_shipping_classes ? '_class_' . $shipping_class_id : '' ),
				'type'     => 'text',
				'default'  => '',
			),
		) );
	}
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_time_options',
	),
) );
return $settings;
