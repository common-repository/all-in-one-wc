<?php
/**
 * Invoicing Module - Invoice
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Generate;

if ( ! class_exists( 'Invoice' ) ) {

	/**
	 * Declare class `Invoice`.
	 */
	class Invoice {

		/**
		 * Order ID.
		 *
		 * @var int $order_id Order ID.
		 */
		public $order_id;

		/**
		 * Invoice Type.
		 *
		 * @var string $invoice_type Invoice Type.
		 */
		public $invoice_type;

		/**
		 * Constructor.
		 *
		 * @param int    $order_id Order ID.
		 * @param string $invoice_type Invoice type.
		 */
		function __construct( $order_id, $invoice_type ) {
			$this->order_id     = $order_id;
			$this->invoice_type = $invoice_type;
		}

		/**
		 * Is created.
		 *
		 * @return string
		 */
		function is_created() {
			return ( '' != get_post_meta( $this->order_id, '_aiow_invoicing_' . $this->invoice_type . '_date', true ) );
		}

		/**
		 * Delete.
		 */
		function delete() {
			update_post_meta( $this->order_id, '_aiow_invoicing_' . $this->invoice_type . '_number_id', 0 );
			update_post_meta( $this->order_id, '_aiow_invoicing_' . $this->invoice_type . '_date', '' );
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_sequential_enabled', 'no' ) ) {
				$option_name = 'aiow_invoicing_' . $this->invoice_type . '_numbering_counter';
				$the_invoice_counter = aiow_option( $option_name, 1 );
				update_option( $option_name, ( $the_invoice_counter - 1 ) );
			}
		}

		/**
		 * Create PDF.
		 *
		 * @param string $date Date.
		 * @return string
		 */
		function create( $date = '' ) {
			$order_id = $this->order_id;
			$invoice_type = $this->invoice_type;
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type . '_skip_zero_total', 'no' ) ) {
				$_order = wc_get_order( $order_id );
				if ( 0 == $_order->get_total() ) {
					return;
				}
			}
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type . '_sequential_enabled', 'no' ) ) {
				$the_invoice_number = aiow_option( 'aiow_invoicing_' . $invoice_type . '_numbering_counter', 1 );
				update_option( 'aiow_invoicing_' . $invoice_type . '_numbering_counter', ( $the_invoice_number + 1 ) );
			} else {
				$the_invoice_number = $order_id;
			}
			$the_date = ( '' == $date ) ? current_time( 'timestamp' ) : $date;
			update_post_meta( $order_id, '_aiow_invoicing_' . $invoice_type . '_number_id', $the_invoice_number );
			update_post_meta( $order_id, '_aiow_invoicing_' . $invoice_type . '_date', $the_date );
		}

		/**
		 * Get file name.
		 *
		 * @return string
		 */
		function get_file_name() {
			$_file_name = sanitize_file_name( do_shortcode( aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_file_name', '' ) ) );
			if ( '' === $_file_name ) {
				$_file_name = $this->invoice_type . '-' . $this->order_id;
			}
			return apply_filters( 'aiow_get_' . $this->invoice_type . '_file_name', $_file_name . '.pdf', $this->order_id );
		}

		/**
		 * Get invoice date.
		 *
		 * @return string
		 */
		function get_invoice_date() {
			$the_date = get_post_meta( $this->order_id, '_aiow_invoicing_' . $this->invoice_type . '_date', true );
			return apply_filters( 'aiow_get_' . $this->invoice_type . '_date', $the_date, $this->order_id );
		}

		/**
		 * Get invoice number.
		 *
		 * @return string
		 */
		function get_invoice_number() {
			$replaced_values = array(
				'%prefix%'  => aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_numbering_prefix', '' ),
				'%counter%' => sprintf( '%0' . aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_numbering_counter_width', 0 ) . 'd',
					get_post_meta( $this->order_id, '_aiow_invoicing_' . $this->invoice_type . '_number_id', true ) ),
				'%suffix%'  => aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_numbering_suffix', '' ),
			);
			return apply_filters( 'aiow_get_' . $this->invoice_type . '_number',
				do_shortcode( str_replace( array_keys( $replaced_values ), $replaced_values,
					get_option( 'aiow_invoicing_' . $this->invoice_type . '_numbering_template', '%prefix%%counter%%suffix%' ) ) ),
				$this->order_id
			);
		}

	}
}
