<?php
/**
 * All In One For WooCommerce - Settings - Call for Price
 *
 * @package WordPress
 * @package WooCommerce
 */

return array(
	array(
		'title'    => __( 'Call for Price Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'Leave price empty when adding or editing products. Then set the options here.', 'all-in-one-wc' ) .
			' ' . __( 'You can use shortcodes in options.', 'all-in-one-wc' ),
		'id'       => 'aiow_call_for_price_options',
	),
	array(
		'title'    => __( 'Label to Show on Single', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets the html to output on empty price. Leave blank to disable.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_call_for_price_text',
		'default'  => '<strong>Call for price</strong>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label to Show on Archives', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets the html to output on empty price. Leave blank to disable.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_call_for_price_text_on_archive',
		'default'  => '<strong>Call for price</strong>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label to Show on Homepage', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets the html to output on empty price. Leave blank to disable.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_call_for_price_text_on_home',
		'default'  => '<strong>Call for price</strong>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label to Show on Related', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets the html to output on empty price. Leave blank to disable.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_call_for_price_text_on_related',
		'default'  => '<strong>Call for price</strong>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Label to Show for Variations', 'all-in-one-wc' ),
		'desc_tip' => __( 'This sets the html to output on empty price. Leave blank to disable.', 'all-in-one-wc' ),
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'id'       => 'aiow_call_for_price_text_variation',
		'default'  => '<strong>Call for price</strong>',
		'type'     => 'textarea',
		'css'      => 'width:100%',
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
	),
	array(
		'title'    => __( 'Hide Sale! Tag', 'all-in-one-wc' ),
		'desc'     => __( 'Hide the tag', 'all-in-one-wc' ),
		'id'       => 'aiow_call_for_price_hide_sale_sign',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Make All Products Call for Price', 'all-in-one-wc' ),
		'desc'     => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip' => __( 'Enable this to make all products prices empty. When checkbox disabled, all prices go back to normal.', 'all-in-one-wc' ),
		'id'       => 'aiow_call_for_price_make_all_empty',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_call_for_price_options',
	),
);
