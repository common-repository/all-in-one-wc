<?php
/**
 * All In One For WooCommerce Add to Cart per Product.
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\AddToCart\Product;

if ( ! class_exists( 'Product' ) ) :

class Product {

	/**
	 * Constructor.
	 */
	function __construct() {
		if ( 'yes' === aiow_option( 'aiow_add_to_cart_per_product_enabled' ) ) {
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_button_text_single' ),  PHP_INT_MAX );
			add_filter( 'woocommerce_product_add_to_cart_text',        array( $this, 'change_add_to_cart_button_text_archive' ), PHP_INT_MAX );
			add_action( 'add_meta_boxes',                              array( $this, 'add_custom_add_to_cart_meta_box' ) );
			add_action( 'save_post_product',                           array( $this, 'save_custom_add_to_cart_meta_box' ), 100, 2 );
		}
	}

	/**
	 * Change add to cart button text in single page.
	 *
	 * @param string $add_to_cart_text  Add to cart button
	 * @return string
	 */
	function change_add_to_cart_button_text_single( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'single' );
	}

	/**
	 * Change add to cart button text in archive page.
	 *
	 * @param string $add_to_cart_text  Add to cart button
	 * @return string
	 */
	function change_add_to_cart_button_text_archive( $add_to_cart_text ) {
		return $this->change_add_to_cart_button_text( $add_to_cart_text, 'archive' );
	}

	/**
	 * Change add to cart button text.
	 *
	 * @param string $add_to_cart_text   Add to cart button.
	 * @param string $single_or_archive  Single OR archive.
	 * @return string
	 */
	function change_add_to_cart_button_text( $add_to_cart_text, $single_or_archive ) {
		global $product;
		if ( ! $product ) {
			return $add_to_cart_text;
		}
		$local_custom_add_to_cart_option_id = 'aiow_custom_add_to_cart_local_' . $single_or_archive;
		$local_custom_add_to_cart_option_value = get_post_meta( aiow_get_product_id_or_variation_parent_id( $product ), '_' . $local_custom_add_to_cart_option_id, true );
		if ( '' != $local_custom_add_to_cart_option_value ) {
			return $local_custom_add_to_cart_option_value;
		}
		return $add_to_cart_text;
	}

	/**
	 * Save custom add to cart meta box.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post Post Object.
	 */
	function save_custom_add_to_cart_meta_box( $post_id, $post ) {
		// Check that we are saving with custom add to cart metabox displayed.
		if ( ! isset( $_POST['wooaiow_custom_add_to_cart_save_post'] ) ) {
			return;
		}
		$option_name = 'aiow_custom_add_to_cart_local_' . 'single';
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
		$option_name = 'aiow_custom_add_to_cart_local_' . 'archive';
		update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
	}

	/**
	 * Add custom add to cart metabox.
	 */
	function add_custom_add_to_cart_meta_box() {
		add_meta_box(
			'wc-jetpack-custom-add-to-cart',
			__( 'Add to Cart Button', 'all-in-one-wc' ),
			array( $this, 'create_custom_add_to_cart_meta_box' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * Create custom add to cart metabox.
	 */
	function create_custom_add_to_cart_meta_box() {

		$current_post_id = get_the_ID();

		$options = array(
			'single'  => __( 'Single product view', 'all-in-one-wc' ),
			'archive' => __( 'Product category (archive) view', 'all-in-one-wc' ),
		);

		$html = '<table style="width:50%;min-width:300px;">';
		foreach ( $options as $option_key => $option_desc ) {
			$option_type = 'textarea';
			if ( 'url' == $option_key )
				$option_type = 'text';
			$html .= '<tr>';
			$html .= '<th>' . $option_desc . '</th>';

			$option_id = 'aiow_custom_add_to_cart_local_' . $option_key;
			$option_value = get_post_meta( $current_post_id, '_' . $option_id, true );

			if ( 'textarea' === $option_type )
				$html .= '<td style="width:80%;">';
			else
				$html .= '<td>';
					$html .= '<textarea style="width:100%;" id="' . $option_id . '" name="' . $option_id . '">' . $option_value . '</textarea>';
			$html .= '</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		$html .= '<input type="hidden" name="wooaiow_custom_add_to_cart_save_post" value="wooaiow_custom_add_to_cart_save_post">';
		echo $html;
	}
}

endif;
