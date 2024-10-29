<?php
/**
 * All In One For WooCommerce - Settings - Add to Cart
 *
 * @package WordPress
 * @package WooCommerce
 */

$settings = array(
	array(
		'title'    => __( 'Per Category Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This sections lets you set Add to Cart button text on per category basis.', 'all-in-one-wc' ),
		'id'       => 'aiow_add_to_cart_per_category_options',
	),
	array(
		'title'    => __( 'Per Category Labels', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable Section', 'all-in-one-wc' ) . '</strong>',
		'desc_tip' => '',
		'id'       => 'aiow_add_to_cart_per_category_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Category Groups Number', 'all-in-one-wc' ),
		'desc_tip' => __( 'Click "Save changes" after you change this number.', 'all-in-one-wc' ),
		'id'       => 'aiow_add_to_cart_per_category_total_groups_number',
		'default'  => 1,
		'type'     => 'aiow_custom_number',
		'desc'     => apply_filters( 'aiow_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'aiow_message', '', 'readonly' ) ) ? apply_filters( 'aiow_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min'  => '1' )
		),
	),
);
$product_cats = array();
$product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ){
	foreach ( $product_categories as $product_category ) {
		$product_cats[ $product_category->term_id ] = $product_category->name;
	}
}
for ( $i = 1; $i <= apply_filters( 'aiow_option', 1, aiow_option( 'aiow_add_to_cart_per_category_total_groups_number', 1 ) ); $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'Group', 'all-in-one-wc' ) . ' #' . $i,
			'desc'     => __( 'Enable', 'all-in-one-wc' ),
			'id'       => 'aiow_add_to_cart_per_category_enabled_group_' . $i,
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'desc'     => __( 'categories', 'all-in-one-wc' ),
			'desc_tip' => '',
			'id'       => 'aiow_add_to_cart_per_category_ids_group_' . $i,
			'default'  => '',
			'type'     => 'multiselect',
			'class'    => 'chosen_select',
			'options'  => $product_cats,
		),
		array(
			'desc'     => __( 'Button text - single product view', 'all-in-one-wc' ),
			'id'       => 'aiow_add_to_cart_per_category_text_single_group_' . $i,
			'default'  => '',
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Button text - product archive (category) view', 'all-in-one-wc' ),
			'id'       => 'aiow_add_to_cart_per_category_text_archive_group_' . $i,
			'default'  => '',
			'type'     => 'textarea',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_add_to_cart_per_category_options',
	),
	array(
		'title'    => __( 'Per Product Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => __( 'This section lets you set Add to Cart button text on per product basis. When enabled, label for each product can be changed in "Edit Product".', 'all-in-one-wc' ),
		'id'       => 'aiow_add_to_cart_per_product_options',
	),
	array(
		'title'    => __( 'Per Product Labels', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable Section', 'all-in-one-wc' ) . '</strong>',
		'desc_tip' => '',
		'id'       => 'aiow_add_to_cart_per_product_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_add_to_cart_per_product_options',
	),
	array(
		'title'    => __( 'Per Product Type Options', 'all-in-one-wc' ),
		'type'     => 'title',
		'desc'     => 'This sections lets you set text for add to cart button for various products types and various conditions.',
		'id'       => 'aiow_add_to_cart_text_options',
	),
	array(
		'title'    => __( 'Per Product Type Labels', 'all-in-one-wc' ),
		'desc'     => '<strong>' . __( 'Enable Section', 'all-in-one-wc' ) . '</strong>',
		'id'       => 'aiow_add_to_cart_text_enabled',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
) );
$groups_by_product_type = array(
	array(
		'id'       => 'simple',
		'title'    => __( 'Simple product', 'all-in-one-wc' ),
		'default'  => __( 'Add to cart', 'woocommerce' ),
	),
	array(
		'id'       => 'variable',
		'title'    => __( 'Variable product', 'all-in-one-wc' ),
		'default'  => __( 'Select options', 'woocommerce' ),
	),
	array(
		'id'       => 'external',
		'title'    => __( 'External product', 'all-in-one-wc' ),
		'default'  => __( 'Buy product', 'woocommerce' ),
	),
	array(
		'id'       => 'grouped',
		'title'    => __( 'Grouped product', 'all-in-one-wc' ),
		'default'  => __( 'View products', 'woocommerce' ),
	),
	array(
		'id'       => 'other',
		'title'    => __( 'Other product', 'all-in-one-wc' ),
		'default'  => __( 'Read more', 'woocommerce' ),
	),
);
foreach ( $groups_by_product_type as $group_by_product_type ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => $group_by_product_type['title'],
			'id'       => 'aiow_add_to_cart_text_on_single_' . $group_by_product_type['id'],
			'desc'     => __( 'Single product view.', 'all-in-one-wc' ),
			'desc_tip' => __( 'Leave blank to disable.', 'all-in-one-wc' ) . ' ' . __( 'Default: ', 'all-in-one-wc' ) . $group_by_product_type['default'],
			'default'  => $group_by_product_type['default'],
			'type'     => 'text',
		),
		array(
			'id'       => 'aiow_add_to_cart_text_on_archives_' . $group_by_product_type['id'],
			'desc'     => __( 'Product category (archive) view.', 'all-in-one-wc' ),
			'desc_tip' => __( 'Leave blank to disable.', 'all-in-one-wc' ) . ' ' . __( 'Default: ', 'all-in-one-wc' ) . $group_by_product_type['default'],
			'default'  => $group_by_product_type['default'],
			'type'     => 'text',
		),
	) );
	if ( 'variable' !== $group_by_product_type['id'] )
		$settings = array_merge( $settings, array(
			array(
				'desc'     => __( 'Products not in stock. Product category (archive) view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Add to cart', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_archives_not_in_stock_' . $group_by_product_type['id'],
				'default'  => __( 'Read more', 'all-in-one-wc' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Products on sale. Single product view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Add to cart', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_single_sale_' . $group_by_product_type['id'],
				'default'  => __( 'Add to cart', 'all-in-one-wc' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Products on sale. Product category (archive) view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Add to cart', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_archives_sale_' . $group_by_product_type['id'],
				'default'  => __( 'Add to cart', 'all-in-one-wc' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Products with price set to 0 (i.e. free). Single product view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Add to cart', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_single_zero_price_' . $group_by_product_type['id'],
				'default'  => __( 'Add to cart', 'all-in-one-wc' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Products with price set to 0 (i.e. free). Product category (archive) view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Add to cart', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_archives_zero_price_' . $group_by_product_type['id'],
				'default'  => __( 'Add to cart', 'all-in-one-wc' ),
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Products with empty price. Product category (archive) view.', 'all-in-one-wc' ),
				'desc_tip' => __( 'Leave blank to disable. Default: Read More', 'all-in-one-wc' ),
				'id'       => 'aiow_add_to_cart_text_on_archives_no_price_' . $group_by_product_type['id'],
				'default'  => __( 'Read more', 'all-in-one-wc' ),
				'type'     => 'text',
			),
		) );
	if ( 'external' === $group_by_product_type['id'] ) {
		continue;
	}
	$settings = array_merge( $settings, array(
		array(
			'id'       => 'aiow_add_to_cart_text_on_single_in_cart_' . $group_by_product_type['id'],
			'desc'     => __( 'Already in cart. Single product view.', 'all-in-one-wc' ),
			'desc_tip' => __( 'Leave blank to disable.', 'all-in-one-wc' ) . ' ' .
				__( 'Try: ', 'all-in-one-wc' ) . __( 'Already in cart - Add Again?', 'all-in-one-wc' ) . ' ' .
				__( 'Default: ', 'all-in-one-wc' ) . __( 'Add to cart', 'all-in-one-wc' ),
			'default'  => __( 'Add to cart', 'all-in-one-wc' ),
			'type'     => 'text',
		),
		array(
			'id'       => 'aiow_add_to_cart_text_on_archives_in_cart_' . $group_by_product_type['id'],
			'desc'     => __( 'Already in cart. Product category (archive) view.', 'all-in-one-wc' ),
			'desc_tip' => __( 'Leave blank to disable.', 'all-in-one-wc' ) . ' ' .
				__( 'Try: ', 'all-in-one-wc' ) . __( 'Already in cart - Add Again?', 'all-in-one-wc' ) . ' ' .
				__( 'Default: ', 'all-in-one-wc' ) . __( 'Add to cart', 'all-in-one-wc' ),
			'default'  => __( 'Add to cart', 'all-in-one-wc' ),
			'type'     => 'text',
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'aiow_add_to_cart_text_options',
	),
) );
return $settings;
