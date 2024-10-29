<?php
/**
 * All In One For WooCommerce - Settings - Custom Price Labels
 *
 * @package WordPress
 * @package WooCommerce
 */

$product_cats = aiow_get_terms( 'product_cat' );
$products     = aiow_get_products();

return array(
	array(
		'title'     => __( 'Custom Price Labels - Per Product', 'all-in-one-wc' ),
		'type'      => 'title',
		'id'        => 'aiow_local_price_labels_options'
	),
	array(
		'title'     => __( 'Per Product', 'all-in-one-wc' ),
		'desc'      => __( 'Enable', 'all-in-one-wc' ),
		'desc_tip'  => __( 'This will add metaboxes to each product\'s admin edit page.', 'all-in-one-wc' ),
		'id'        => 'aiow_local_price_labels_enabled',
		'default'   => 'yes',
		'type'      => 'checkbox',
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'aiow_local_price_labels_options',
	),
	array(
		'title'     => __( 'Custom Price Labels - Globally', 'all-in-one-wc' ),
		'type'      => 'title',
		'desc'      => __( 'This section lets you set price labels for all products globally.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_options',
	),
	array(
		'title'     => __( 'Add before the price', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to add before all products prices. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_add_before_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'desc'      => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Add after the price', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to add after all products prices. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_add_after_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Add between regular and sale prices', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to add between regular and sale prices. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_between_regular_and_sale_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'desc'      => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Remove from price', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to remove from all products prices. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_remove_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'desc'      => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Replace in price', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to replace in all products prices. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_replace_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'desc'      => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes'
					=> apply_filters( 'aiow_message', '', 'readonly' ),
		'css'       => 'width:100%;',
	),
	array(
		'title'     => '',
		'desc_tip'  => __( 'Enter text to replace with. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_replace_with_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'desc'      => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => apply_filters( 'aiow_message', '', 'readonly' ),
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Instead of the price', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Enter text to display instead of the price. Leave blank to disable.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_instead_text',
		'default'   => '',
		'type'      => 'custom_textarea',
		'css'       => 'width:100%;',
	),
	array(
		'title'     => __( 'Products - Include', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Apply global price labels only for selected products. Leave blank to disable the option.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_products_incl',
		'default'   => '',
		'type'      => 'multiselect',
		'class'     => 'chosen_select',
		'options'   => $products,
	),
	array(
		'title'     => __( 'Products - Exclude', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Do not apply global price labels only for selected products. Leave blank to disable the option.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_products_excl',
		'default'   => '',
		'type'      => 'multiselect',
		'class'     => 'chosen_select',
		'options'   => $products,
	),
	array(
		'title'     => __( 'Product Categories - Include', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Apply global price labels only for selected product categories. Leave blank to disable the option.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_product_cats_incl',
		'default'   => '',
		'type'      => 'multiselect',
		'class'     => 'chosen_select',
		'options'   => $product_cats,
	),
	array(
		'title'     => __( 'Product Categories - Exclude', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Do not apply global price labels only for selected product categories. Leave blank to disable the option.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_product_cats_excl',
		'default'   => '',
		'type'      => 'multiselect',
		'class'     => 'chosen_select',
		'options'   => $product_cats,
	),
	array(
		'title'     => __( 'Product Types - Include', 'all-in-one-wc' ),
		'desc_tip'  => __( 'Apply global price labels only for selected product types. Leave blank to disable the option.', 'all-in-one-wc' ),
		'id'        => 'aiow_global_price_labels_product_types_incl',
		'default'   => '',
		'type'      => 'multiselect',
		'class'     => 'chosen_select',
		'options'   => array_merge( wc_get_product_types(), array( 'variation' => __( 'Variable product\'s variation', 'all-in-one-wc' ) ) ),
	),
	array(
		'type'      => 'sectionend',
		'id'        => 'aiow_global_price_labels_options',
	),
);
