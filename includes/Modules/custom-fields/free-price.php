<?php
/**
 * All In One For WooCommerce - Settings - Free Price
 *
 * @package WordPress
 * @package WooCommerce
 */

$product_types = array(
	'simple'   => __( 'Simple and Custom Products', 'all-in-one-wc' ),
	'variable' => __( 'Variable Products', 'all-in-one-wc' ),
	'grouped'  => __( 'Grouped Products', 'all-in-one-wc' ),
	'external' => __( 'External Products', 'all-in-one-wc' ),
);
$views = array(
	'single'   => __( 'Single Product Page', 'all-in-one-wc' ),
	'related'  => __( 'Related Products', 'all-in-one-wc' ),
	'home'     => __( 'Homepage', 'all-in-one-wc' ),
	'page'     => __( 'Pages (e.g. Shortcodes)', 'all-in-one-wc' ),
	'archive'  => __( 'Archives (Product Categories)', 'all-in-one-wc' ),
);
$settings = array();
foreach ( $product_types as $product_type => $product_type_desc ) {
	$default_value = ( 'simple' === $product_type || 'external' === $product_type ) ? '<span class="amount">' . __( 'Free!', 'woocommerce' ) . '</span>' : __( 'Free!', 'woocommerce' );
	$settings = array_merge( $settings, array(
		array(
			'title'    => $product_type_desc,
			'desc'     => __( 'Labels can contain shortcodes.', 'all-in-one-wc' ),
			'type'     => 'title',
			'id'       => 'aiow_free_price_' . $product_type . 'options',
		),
	) );
	$current_views = $views;
	if ( 'variable' === $product_type ) {
		$current_views['variation'] = __( 'Variations', 'all-in-one-wc' );
	}
	foreach ( $current_views as $view => $view_desc ) {
		$settings = array_merge( $settings, array(
			array(
				'title'    => $view_desc,
				'id'       => 'aiow_free_price_' . $product_type . '_' . $view,
				'default'  => $default_value,
				'type'     => 'textarea',
				'css'      => 'width:30%;min-width:300px;min-height:50px;',
				'desc'     => ( 'variable' === $product_type ) ? apply_filters( 'aiow_message', '', 'desc' ) : '',
				'custom_attributes' => ( 'variable' === $product_type ) ? apply_filters( 'aiow_message', '', 'readonly' ) : '',
			),
		) );
	}
	$settings = array_merge( $settings, array(
		array(
			'type'     => 'sectionend',
			'id'       => 'aiow_free_price_' . $product_type . 'options',
		),
	) );
}
return $settings;
