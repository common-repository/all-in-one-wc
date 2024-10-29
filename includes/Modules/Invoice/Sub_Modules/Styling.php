<?php
/**
 * Submodule PDF Invoicing - Styling.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Styling' ) ) {

	/**
	 * Declare class `Styling` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Styling extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_styling';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Styling', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );
			add_action( 'init',                          array( $this, 'manually_download_fonts' ) );
			add_action( 'init',                          array( $this, 'schedule_download_fonts_event' ) );
			add_action( 'admin_init',                    array( $this, 'schedule_download_fonts_event' ) );
			add_action( 'aiow_download_tcpdf_fonts_hook', array( $this, 'download_fonts' ) );
		}

		/**
		 * Get default CSS template.
		 *
		 * @param string $invoice_type_id Invoice Type ID.
		 * @return string
		 */
		function get_default_css_template( $invoice_type_id ) {
			if ( ! isset( $this->default_css_template[ $invoice_type_id ] ) ) {
				$default_template_filename = ( false === strpos( $invoice_type_id, 'custom_doc_' ) ? $invoice_type_id : 'custom_doc' );
				$default_template_filename = aiow_plugin_path() . '/assets/pdf/' . $default_template_filename . '.css';
				if ( file_exists( $default_template_filename ) ) {
					ob_start();
					include( $default_template_filename );
					$this->default_css_template[ $invoice_type_id ] = ob_get_clean();
				} else {
					$this->default_css_template[ $invoice_type_id ] = '';
				}
			}
			return $this->default_css_template[ $invoice_type_id ];
		}

		/**
		 * On an early action hook, check if the hook is scheduled - if not, schedule it.
		 */
		function schedule_download_fonts_event() {
			$interval        = 'hourly';
			$event_hook      = 'aiow_download_tcpdf_fonts_hook';
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( ! $event_timestamp ) {
				wp_schedule_event( time(), $interval, $event_hook, array( $interval ) );
			}
		}

		/**
		 * Download fonts.
		 *
		 * @param int $interval Interval.
		 */
		function download_fonts( $interval ) {
			update_option( 'aiow_download_tcpdf_fonts_hook_timestamp', (int) current_time( 'timestamp' ) );
			aiow_check_and_maybe_download_tcpdf_fonts( true );
		}

		/**
		 * Manually download fonts.
		 */
		function manually_download_fonts() {
			if ( isset( $_GET['aiow_download_fonts'] ) ) {
				delete_option( 'aiow_invoicing_fonts_version' );
				delete_option( 'aiow_invoicing_fonts_version_timestamp' );
				aiow_check_and_maybe_download_tcpdf_fonts();
				wp_safe_redirect( remove_query_arg( 'aiow_download_fonts' ) );
				exit;
			}
		}

	}
}
