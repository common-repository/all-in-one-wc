<?php
/**
 * Invoicing Module - TCPDF
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Generate;

if ( ! class_exists( 'TC_PDF' ) ) {
	// Enable custom TCPDF config.
	define( 'K_TCPDF_EXTERNAL_CONFIG', true );
	require_once plugin_dir_path( __FILE__ ) . '_config.php';

	/**
	 * Declare class `TC_PDF` extends to `\TCPDF`.
	 */
	class TC_PDF extends \TCPDF {

		/**
		 * Set invoice type.
		 *
		 * @param string $invoice_type Invoice type.
		 */
		function set_invoice_type( $invoice_type ) {
			$this->invoice_type = $invoice_type;
		}

		/**
		 * Page footer.
		 */
		function Footer() {
			$invoice_type = $this->invoice_type;
			$footer_text = aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_text', __( 'Page %page_number% / %total_pages%', 'all-in-one-wc' ) );
			$footer_text = str_replace( '%page_number%', $this->getAliasNumPage(), $footer_text );
			$footer_text = str_replace( '%total_pages%', $this->getAliasNbPages(), $footer_text );
			$border_desc = array(
				'T' => array(
					'color' => aiow_hex2rgb( aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_line_color', '#cccccc' ) ),
					'width' => 0,
				),
			);
			$footer_text_color_rgb = aiow_hex2rgb( aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_text_color', '#cccccc' ) );
			$this->SetTextColor( $footer_text_color_rgb[0], $footer_text_color_rgb[1], $footer_text_color_rgb[2] );
			$this->writeHTMLCell( 0, 0, '', '', do_shortcode( $footer_text ), $border_desc, 1, 0, true, '', true );
		}
	}
}
