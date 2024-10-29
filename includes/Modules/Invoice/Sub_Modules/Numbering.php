<?php
/**
 * Submodule PDF Invoicing - Numbering.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Numbering' ) ) {

	/**
	 * Declare class `Footer` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Numbering extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_numbering';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Numbering', 'all-in-one-wc' );
			parent::__construct( 'submodule' );

			if ( 'yes' === aiow_option( 'aiow_invoicing_admin_search_by_invoice', 'no' ) ) {
				add_action( 'pre_get_posts', array( $this, 'search_orders_by_invoice_number' ) );
			}
		}

		/**
		 * Search order by invoice number|ID.
		 *
		 * @param object $query WP_Query.
		 * @return mixed
		 */
		function search_orders_by_invoice_number( $query ) {
			if (
				! is_admin() ||
				! isset( $query->query ) ||
				! isset( $query->query['s'] ) ||
				false === is_numeric( $query->query['s'] ) ||
				0 == $query->query['s'] ||
				'shop_order' !== $query->query['post_type'] ||
				! $query->query_vars['shop_order_search']
			) {
				return;
			}
			$invoice_number = $query->query['s'];
			$query->query_vars['post__in'] = array();
			$query->query['s'] = '';
			$query->set( 'meta_key', '_aiow_invoicing_invoice_number_id' );
			$query->set( 'meta_value', $invoice_number );
		}

	}
}
