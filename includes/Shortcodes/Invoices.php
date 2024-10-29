<?php
/**
 * Register Invoices Shortcode
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Shortcodes;

if ( ! class_exists( 'Invoices' ) ) {

	/**
	 * Declare `Invoices` class extends `Shortcodes`
	 */
	class Invoices extends Shortcodes {

		/**
		 * Constructor.
		 */
		function __construct() {

			$this->the_shortcodes = array(
				'aiow_invoice_number',
				'aiow_proforma_invoice_number',
				'aiow_packing_slip_number',
				'aiow_credit_note_number',
				'aiow_custom_doc_number',
				'aiow_invoice_date',
				'aiow_proforma_invoice_date',
				'aiow_packing_slip_date',
				'aiow_credit_note_date',
				'aiow_custom_doc_date',
			);

			$this->the_atts = array(
				'order_id'     => 0,
				'date_format'  => aiow_option( 'date_format' ),
				'days'         => 0,
				'invoice_type' => 'invoice',
				'doc_nr'       => 1,
			);

			parent::__construct();
		}

		/**
		 * Init attributes.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function init_atts( $atts ) {
			if ( 0 == $atts['order_id'] ) {
				$atts['order_id'] = ( isset( $_GET['order_id'] ) ) ? $_GET['order_id'] : get_the_ID();
				if ( 0 == $atts['order_id'] ) return false;
			}
			if ( 'shop_order' !== get_post_type( $atts['order_id'] ) ) return false;
			return $atts;
		}

		/**
		 * Invoice date.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_invoice_date( $atts ) {
			return aiow_get_invoice_date( $atts['order_id'], $atts['invoice_type'], $atts['days'], $atts['date_format'] );
		}

		/**
		 * Proforma invoice date.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_proforma_invoice_date( $atts ) {
			return aiow_get_invoice_date( $atts['order_id'], 'proforma_invoice', $atts['days'], $atts['date_format'] );
		}

		/**
		 * Packing slip date.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_packing_slip_date( $atts ) {
			return aiow_get_invoice_date( $atts['order_id'], 'packing_slip', $atts['days'], $atts['date_format'] );
		}

		/**
		 * Credit note date.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_credit_note_date( $atts ) {
			return aiow_get_invoice_date( $atts['order_id'], 'credit_note', $atts['days'], $atts['date_format'] );
		}

		/**
		 * Custom document date.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_custom_doc_date( $atts ) {
			$invoice_type_id = ( 1 == $atts['doc_nr'] ) ? 'custom_doc' :  'custom_doc' . '_' . $atts['doc_nr'];
			return aiow_get_invoice_date( $atts['order_id'], $invoice_type_id, $atts['days'], $atts['date_format'] );
		}

		/**
		 * Credit invoice number.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_invoice_number( $atts ) {
			return aiow_get_invoice_number( $atts['order_id'], $atts['invoice_type'] );
		}

		/**
		 * Proforma invoice number.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_proforma_invoice_number( $atts ) {
			return aiow_get_invoice_number( $atts['order_id'], 'proforma_invoice' );
		}

		/**
		 * Packing slip number.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_packing_slip_number( $atts ) {
			return aiow_get_invoice_number( $atts['order_id'], 'packing_slip' );
		}

		/**
		 * Credit note number.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_credit_note_number( $atts ) {
			return aiow_get_invoice_number( $atts['order_id'], 'credit_note' );
		}

		/**
		 * Custom document number.
		 *
		 * @param array $atts Attributes.
		 * @return array
		 */
		function aiow_custom_doc_number( $atts ) {
			$invoice_type_id = ( 1 == $atts['doc_nr'] ) ? 'custom_doc' :  'custom_doc' . '_' . $atts['doc_nr'];
			return aiow_get_invoice_number( $atts['order_id'], $invoice_type_id );
		}
	}
}
