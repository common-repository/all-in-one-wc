<?php
/**
 * Shipping - Settings - Shipping Descriptions
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => sprintf( __( 'This section will allow you to add any text (e.g. description) for shipping method. Text will be visible on cart and checkout pages. You can add HTML tags here, e.g. try %s.', 'all-in-one-wc' ),
			'<code>' . esc_html( '<br><small>Your shipping description.</small>' ) . '</code>' ),
		'id'       => 'aiow_shipping_description_options',
	),
	array(
		'title'    => __( 'Description Visibility', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_descriptions_visibility',
		'default'  => 'both',
		'type'     => 'select',
		'options'  => array(
			'both'          => __( 'On both cart and checkout pages', 'all-in-one-wc' ),
			'cart_only'     => __( 'Only on cart page', 'all-in-one-wc' ),
			'checkout_only' => __( 'Only on checkout page', 'all-in-one-wc' ),
		),
		'desc_tip' => __( 'Possible values: on both cart and checkout pages; only on cart page; only on checkout page.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'title'    => __( 'Description Position', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_descriptions_position',
		'default'  => 'after',
		'type'     => 'select',
		'options'  => array(
			'after'   => __( 'After the label', 'all-in-one-wc' ),
			'before'  => __( 'Before the label', 'all-in-one-wc' ),
			'instead' => __( 'Instead of the label', 'all-in-one-wc' ),
		),
		'desc_tip' => __( 'Possible values: after the label; before the label; instead of the label.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_description_options',
	),
	array(
		'title'    => __( 'Shipping Methods Descriptions', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_description_methods_options',
	),
	array(
		'title'    => __( 'Use Shipping Instances', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this if you want to use shipping methods instances instead of shipping methods.', 'all-in-one-wc' ) . ' ' .
			__( 'Save changes after enabling this option.', 'all-in-one-wc' ),
		'type'     => 'checkbox',
		'id'       => 'aiow_shipping_descriptions_use_shipping_instance',
		'default'  => 'no',
	),
);
$use_shipping_instances = ( 'yes' === aiow_option( 'aiow_shipping_descriptions_use_shipping_instance', 'no' ) );
$shipping_methods       = ( $use_shipping_instances ? aiow_get_shipping_methods_instances( true ) : WC()->shipping()->get_shipping_methods() );
foreach ( $shipping_methods as $method ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => ( $use_shipping_instances ? $method['zone_name'] . ': ' . $method['shipping_method_title'] : $method->method_title ),
			'id'       => 'aiow_shipping_description_' . ( $use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id ),
			'default'  => '',
			'type'     => 'textarea',
			'css'      => 'width:100%;',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_description_methods_options',
	),
) );
return $settings;
