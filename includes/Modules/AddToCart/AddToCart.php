<?php
/**
 * All In One For WooCommerce - Module - Add to Cart
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\AddToCart;

if ( ! class_exists( 'AddToCart' ) ) :

class AddToCart extends \AIOW\Modules\Register_Modules {

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->id         = 'add_to_cart';
		$this->short_desc = __( 'Add to Cart Button Labels', 'all-in-one-wc' );
		$this->desc       = __( 'Change text for Add to Cart button by product type, by product category or for individual products (Unlimited category group allowed in free version).', 'all-in-one-wc' );
		$this->desc_pro   = __( 'Change text for Add to Cart button by product type, by product category or for individual products.', 'all-in-one-wc' );
		$this->link_slug  = 'woocommerce-add-to-cart-labels';
		parent::__construct();

		if ( $this->is_enabled() ) {
			$pre_category = new Category\Category();
			$product = new Product\Product();
			$product_type = new Product\Product_Type();
		}
	}

}

endif;
