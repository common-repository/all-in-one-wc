<?php
/**
 * Shipping - Address Formats
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

$settings = array(
	array(
		'title'     => __( 'Force Base Country Display', 'all-in-one-wc' ),
		'type'      => 'title',
		'id'        => 'aiow_address_formats_force_country_display_options',
	),
	array(
		'title'     => __( 'Force Base Country Display', 'all-in-one-wc' ),
		'desc'      => __( 'Enable', 'all-in-one-wc' ),
		'id'        => 'aiow_address_formats_force_country_display',
		'default'   => 'no',
		'type'      => 'checkbox',
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'aiow_address_formats_force_country_display_options',
	),
	array(
		'title'     => __( 'Address Formats by Country', 'all-in-one-wc' ),
		'type'      => 'title',
		'id'        => 'aiow_address_formats_country_options',
	),
);
$formats = $this->get_default_address_formats();
foreach ( $formats as $country_code => $format ) {
	$settings = array_merge( $settings, array(
		array(
			'title'     => ( 'default' === $country_code ) ? $country_code : $country_code . ' - ' . aiow_get_country_name_by_code( $country_code ),
			'id'        => 'aiow_address_formats_country_' . $country_code,
			'default'   => $format,
			'type'      => 'textarea',
			'css'       => 'width:300px;height:200px;',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'      => 'sectionend',
		'id'        => 'aiow_address_formats_country_options',
	),
) );
return $settings;
