<?php
/**
 * Submodule PDF Invoicing - Email Options.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Emails' ) ) {

	/**
	 * Declare class `Emails` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Emails extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->id         = 'pdf_invoicing_emails';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Email Options', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );

			if ( $this->is_enabled() ) {
				if ( 'no' === aiow_option( 'aiow_general_advanced_disable_save_sys_temp_dir', 'no' ) ) {
					add_filter( 'woocommerce_email_attachments', array( $this, 'add_pdf_invoice_email_attachment' ), PHP_INT_MAX, 3 );
				}
			}
		}

		/**
		 * Do attach for payment method.
		 *
		 * @param string $invoice_type_id Invoice Type ID.
		 * @param string $payment_method Payment method.
		 * @return bool
		 */
		function do_attach_for_payment_method( $invoice_type_id, $payment_method ) {
			$included_gateways = aiow_option( 'aiow_invoicing_' . $invoice_type_id . '_payment_gateways', array() );
			if ( empty ( $included_gateways ) ) {
				return true; // include all
			}
			return ( in_array( $payment_method, $included_gateways ) );
		}

		/**
		 * Add PDF invoice in email.
		 *
		 * @param string $attachments File attachments.
		 * @param string $status invoice status.
		 * @param object $order Order object.
		 * @return array
		 */
		function add_pdf_invoice_email_attachment( $attachments, $status, $order ) {
			if ( ! $order || ! is_object( $order ) ) {
				return $attachments;
			}
			if ( 'WC_Vendor_Stores_Order' == get_class( $order ) ) {
				$order = $order->get_parent_order( aiow_get_order_id( $order ) );
			}
			if ( ! is_a( $order, 'WC_Order' ) ) {
				return $attachments;
			}
			$invoice_types_ids = aiow_get_enabled_invoice_types_ids();
			$order_id          = aiow_get_order_id( $order );
			foreach ( $invoice_types_ids as $invoice_type_id ) {
				if ( false === $this->do_attach_for_payment_method( $invoice_type_id, aiow_order_get_payment_method( $order ) ) ) {
					continue;
				}
				if ( ! aiow_is_invoice_created( $order_id, $invoice_type_id ) ) {
					continue;
				}
				$send_on_statuses = aiow_option( 'aiow_invoicing_' . $invoice_type_id . '_attach_to_emails', array() );
				if ( '' == $send_on_statuses ) {
					$send_on_statuses = array();
				}
				if ( in_array( $status, $send_on_statuses ) ) {
					$the_invoice = aiow_get_pdf_invoice( $order_id, $invoice_type_id );
					$file_name   = $the_invoice->get_pdf( 'F' );
					if ( '' != $file_name ) {
						$attachments[] = $file_name;
					}
				}
			}
			return $attachments;
		}

	}
}
