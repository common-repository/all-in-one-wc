<?php
/**
 * Shipping - Settings - Custom Shipping
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$wocommerce_shipping_settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping' );
$wocommerce_shipping_settings_url = '<a href="' . $wocommerce_shipping_settings_url . '">' . __( 'WooCommerce > Settings > Shipping', 'all-in-one-wc' ) . '</a>';
$settings = array(
	array(
		'title'    => __( 'Custom Shipping', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_custom_shipping_w_zones_options',
		'desc'     => __( 'This section lets you add custom shipping method.', 'all-in-one-wc' )
			. ' ' . sprintf( __( 'Visit %s to set method\'s options.', 'all-in-one-wc' ), $wocommerce_shipping_settings_url ),
	),
	array(
		'title'    => __( 'Custom Shipping', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_custom_shipping_w_zones_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Admin Title', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_custom_shipping_w_zones_admin_title',
		'default'  => __( 'Custom Shipping', 'all-in-one-wc' ),
		'type'     => 'text',
		'css'      => 'width:300px;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_custom_shipping_w_zones_options',
	),
);
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Custom Shipping (Legacy - without Shipping Zones)', 'all-in-one-wc' ),
		'type'     => 'title',
		'id'       => 'aiow_shipping_custom_shipping_options',
		'desc'     => __( 'This section lets you set number of custom shipping methods to add.', 'all-in-one-wc' )
			. ' ' . sprintf( __( 'After setting the number, visit %s to set each method options.', 'all-in-one-wc' ), $wocommerce_shipping_settings_url ),
	),
	array(
		'title'    => __( 'Custom Shipping Methods Number', 'all-in-one-wc' ),
		'desc_tip' => __( 'Save module\'s settings after changing this option to see new settings fields.', 'all-in-one-wc' ),
		'id'       => 'aiow_shipping_custom_shipping_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'custom_attributes' => array( 'step' => '1', 'min' => '0' ),
	),
) );
for ( $i = 1; $i <= aiow_option( 'aiow_shipping_custom_shipping_total_number', 1 ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Admin Title Custom Shipping', 'all-in-one-wc' ) . ' #' . $i,
			'id'       => 'aiow_shipping_custom_shipping_admin_title_' . $i,
			'default'  => __( 'Custom', 'all-in-one-wc' ) . ' #' . $i,
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_shipping_custom_shipping_options',
	),
) );
return $settings;
