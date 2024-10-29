<?php
/**
 * PDF Invoicing - Advanced
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Advanced' ) ) {

	/**
	 * Declare class `Advanced` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Advanced extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_advanced';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Advanced', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );
		}

		/**
		 * Get default report columns.
		 *
		 * @return array
		 */
		function get_report_default_columns() {
			return array_keys( array(
				'document_number'                    => __( 'Document Number', 'all-in-one-wc' ),
				'document_date'                      => __( 'Document Date', 'all-in-one-wc' ),
				'order_id'                           => __( 'Order ID', 'all-in-one-wc' ),
				'customer_country'                   => __( 'Customer Country', 'all-in-one-wc' ),
				'customer_vat_id'                    => __( 'Customer VAT ID', 'all-in-one-wc' ),
				'tax_percent'                        => __( 'Tax %', 'all-in-one-wc' ),
				'order_total_tax_excluding'          => __( 'Order Total Excl. Tax', 'all-in-one-wc' ),
				'order_taxes'                        => __( 'Order Taxes', 'all-in-one-wc' ),
				'order_total'                        => __( 'Order Total', 'all-in-one-wc' ),
				'order_currency'                     => __( 'Order Currency', 'all-in-one-wc' ),
				'payment_gateway'                    => __( 'Payment Gateway', 'all-in-one-wc' ),
				'refunds'                            => __( 'Refunds', 'all-in-one-wc' ),
			) );
		}

		/**
		 * Get report columns.
		 *
		 * @return array
		 */
		function get_report_columns() {
			return array(
				'document_number'                    => __( 'Document Number', 'all-in-one-wc' ),
				'document_date'                      => __( 'Document Date', 'all-in-one-wc' ),
				'order_id'                           => __( 'Order ID', 'all-in-one-wc' ),
				'customer_country'                   => __( 'Customer Country', 'all-in-one-wc' ),
				'customer_vat_id'                    => __( 'Customer VAT ID', 'all-in-one-wc' ),
				'tax_percent'                        => __( 'Tax %', 'all-in-one-wc' ),
				'order_total_tax_excluding'          => __( 'Order Total Excl. Tax', 'all-in-one-wc' ),
				'order_taxes'                        => __( 'Order Taxes', 'all-in-one-wc' ),
				'order_cart_total_excl_tax'          => __( 'Cart Total Excl. Tax', 'all-in-one-wc' ),
				'order_cart_tax'                     => __( 'Cart Tax', 'all-in-one-wc' ),
				'order_cart_tax_percent'             => __( 'Cart Tax %', 'all-in-one-wc' ),
				'order_shipping_total_excl_tax'      => __( 'Shipping Total Excl. Tax', 'all-in-one-wc' ),
				'order_shipping_tax'                 => __( 'Shipping Tax', 'all-in-one-wc' ),
				'order_shipping_tax_percent'         => __( 'Shipping Tax %', 'all-in-one-wc' ),
				'order_total'                        => __( 'Order Total', 'all-in-one-wc' ),
				'order_currency'                     => __( 'Order Currency', 'all-in-one-wc' ),
				'payment_gateway'                    => __( 'Payment Gateway', 'all-in-one-wc' ),
				'refunds'                            => __( 'Refunds', 'all-in-one-wc' ),
			);
		}

	}
}
